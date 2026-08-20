const fs = require('fs');
const path = require('path');

function parseCSV(text) {
    const lines = [];
    let curToken = '';
    let inQuotes = false;
    let tokens = [];

    for (let i = 0; i < text.length; i++) {
        const c = text[i];
        if (c === '"') {
            if (inQuotes && text[i + 1] === '"') {
                curToken += '"';
                i++;
            } else {
                inQuotes = !inQuotes;
            }
        } else if (c === ',' && !inQuotes) {
            tokens.push(curToken);
            curToken = '';
        } else if ((c === '\r' || c === '\n') && !inQuotes) {
            if (c === '\r' && text[i + 1] === '\n') i++;
            tokens.push(curToken);
            if (tokens.some(t => t.trim().length > 0)) {
                lines.push(tokens);
            }
            tokens = [];
            curToken = '';
        } else {
            curToken += c;
        }
    }
    if (curToken.length > 0 || tokens.length > 0) {
        tokens.push(curToken);
        if (tokens.some(t => t.trim().length > 0)) {
            lines.push(tokens);
        }
    }
    return lines;
}

function escapeSQL(val) {
    if (val === undefined || val === null || val === '—') return 'NULL';
    const str = String(val).trim().replace(/'/g, "''").replace(/\\/g, "\\\\");
    if (str === '' || str === '—') return 'NULL';
    return `'${str}'`;
}

const baseDir = __dirname;

// 1. Read CSV files
const tcContent = fs.readFileSync(path.join(baseDir, 'TestCases_BookingRoom.csv'), 'utf8');
const bugContent = fs.readFileSync(path.join(baseDir, 'BugReport_BookingRoom.csv'), 'utf8');
const wbContent = fs.readFileSync(path.join(baseDir, 'WhiteBoxTesting_BookingRoom.csv'), 'utf8');

const tcRows = parseCSV(tcContent).slice(1);
const bugRows = parseCSV(bugContent).slice(1);
const wbRows = parseCSV(wbContent).slice(1);

let sqlOutput = `-- ==============================================================================
-- DATABASE MANAGEMENT & QA TEST RESULTS FOR BOOKING ROOM THEME
-- Created for QA / Software Tester Team
-- Includes:
--   1. Schema & Data for qa_test_cases (100 Black-Box Test Cases)
--   2. Schema & Data for qa_bug_reports (8 Bug Reports)
--   3. Schema & Data for qa_white_box_tests (12 Code Coverage Tests)
--   4. Analytical Views & QA Metrics Queries
-- ==============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ------------------------------------------------------------------------------
-- Table 1: qa_test_cases
-- Stores functional & non-functional test cases (Black Box Testing)
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS \`qa_test_cases\`;
CREATE TABLE \`qa_test_cases\` (
  \`id\` VARCHAR(20) NOT NULL COMMENT 'Mã Test Case (TC-xxx)',
  \`module\` VARCHAR(100) NOT NULL COMMENT 'Module được kiểm thử',
  \`feature\` VARCHAR(150) NOT NULL COMMENT 'Tính năng / Chức năng',
  \`description\` TEXT COMMENT 'Mô tả mục tiêu test case',
  \`prerequisites\` TEXT COMMENT 'Điều kiện tiên quyết',
  \`steps\` TEXT COMMENT 'Các bước thực hiện',
  \`input\` TEXT COMMENT 'Dữ liệu đầu vào',
  \`expected\` TEXT COMMENT 'Kết quả mong đợi',
  \`priority\` VARCHAR(20) DEFAULT 'Trung bình' COMMENT 'Độ ưu tiên (Cao, Trung bình, Thấp)',
  \`type\` VARCHAR(50) DEFAULT 'Chức năng' COMMENT 'Loại kiểm thử (Chức năng, Validation, UI/UX, Responsive, Security, Performance)',
  \`status\` VARCHAR(20) DEFAULT 'Chưa chạy' COMMENT 'Trạng thái (Đạt, Khung đạt, Lỗi, Chưa chạy)',
  \`notes\` TEXT COMMENT 'Ghi chú kiểm thử / bằng chứng',
  \`created_at\` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (\`id\`),
  KEY \`idx_module\` (\`module\`),
  KEY \`idx_status\` (\`status\`),
  KEY \`idx_priority\` (\`priority\`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu giữ 100 Test Cases kiểm thử Booking Room';

INSERT INTO \`qa_test_cases\` 
(\`id\`, \`module\`, \`feature\`, \`description\`, \`prerequisites\`, \`steps\`, \`input\`, \`expected\`, \`priority\`, \`type\`, \`status\`, \`notes\`) 
VALUES\n`;

const tcValues = tcRows.map(row => {
    const [id, module, feature, desc, prereq, steps, input, expected, priority, type, status, notes] = row;
    return `(${escapeSQL(id)}, ${escapeSQL(module)}, ${escapeSQL(feature)}, ${escapeSQL(desc)}, ${escapeSQL(prereq)}, ${escapeSQL(steps)}, ${escapeSQL(input)}, ${escapeSQL(expected)}, ${escapeSQL(priority)}, ${escapeSQL(type)}, ${escapeSQL(status)}, ${escapeSQL(notes)})`;
});

sqlOutput += tcValues.join(',\n') + ';\n\n';

// ------------------------------------------------------------------------------
// Table 2: qa_bug_reports
// ------------------------------------------------------------------------------
sqlOutput += `-- ------------------------------------------------------------------------------
-- Table 2: qa_bug_reports
-- Stores defect records identified during testing
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS \`qa_bug_reports\`;
CREATE TABLE \`qa_bug_reports\` (
  \`id\` VARCHAR(20) NOT NULL COMMENT 'Mã Bug (BUG-xxx)',
  \`module\` VARCHAR(100) NOT NULL COMMENT 'Module phát sinh lỗi',
  \`title\` VARCHAR(255) NOT NULL COMMENT 'Tên / Tiêu đề lỗi',
  \`severity\` VARCHAR(20) DEFAULT 'Trung bình' COMMENT 'Mức độ nghiêm trọng (Nghiêm trọng, Cao, Trung bình, Thấp)',
  \`priority\` VARCHAR(50) DEFAULT 'P2 - Bình thường' COMMENT 'Độ ưu tiên xử lý',
  \`steps_to_reproduce\` TEXT COMMENT 'Các bước tái hiện lỗi',
  \`expected_result\` TEXT COMMENT 'Kết quả mong đợi',
  \`actual_result\` TEXT COMMENT 'Kết quả thực tế khi xảy ra lỗi',
  \`status\` VARCHAR(20) DEFAULT 'Mới' COMMENT 'Trạng thái (Mới, Đang xử lý, Đã khắc phục, Closed)',
  \`assigned_to\` VARCHAR(100) COMMENT 'Người / Đội ngũ chịu trách nhiệm xử lý',
  \`notes\` TEXT COMMENT 'Ghi chú phương án xử lý',
  \`created_at\` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (\`id\`),
  KEY \`idx_bug_module\` (\`module\`),
  KEY \`idx_bug_status\` (\`status\`),
  KEY \`idx_bug_severity\` (\`severity\`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng quản lý báo cáo lỗi (Bug Reports)';

INSERT INTO \`qa_bug_reports\` 
(\`id\`, \`module\`, \`title\`, \`severity\`, \`priority\`, \`steps_to_reproduce\`, \`expected_result\`, \`actual_result\`, \`status\`, \`assigned_to\`, \`notes\`) 
VALUES\n`;

const bugValues = bugRows.map(row => {
    const [id, module, title, severity, priority, steps, expected, actual, status, assigned, notes] = row;
    return `(${escapeSQL(id)}, ${escapeSQL(module)}, ${escapeSQL(title)}, ${escapeSQL(severity)}, ${escapeSQL(priority)}, ${escapeSQL(steps)}, ${escapeSQL(expected)}, ${escapeSQL(actual)}, ${escapeSQL(status)}, ${escapeSQL(assigned)}, ${escapeSQL(notes)})`;
});

sqlOutput += bugValues.join(',\n') + ';\n\n';

// ------------------------------------------------------------------------------
// Table 3: qa_white_box_tests
// ------------------------------------------------------------------------------
sqlOutput += `-- ------------------------------------------------------------------------------
-- Table 3: qa_white_box_tests
-- Stores source code branch & statement coverage tests (White Box Testing)
-- ------------------------------------------------------------------------------
DROP TABLE IF EXISTS \`qa_white_box_tests\`;
CREATE TABLE \`qa_white_box_tests\` (
  \`id\` VARCHAR(20) NOT NULL COMMENT 'Mã Whitebox Test (WB-xxx)',
  \`function_name\` VARCHAR(150) NOT NULL COMMENT 'Tên hàm / Handler kiểm thử',
  \`file_location\` VARCHAR(255) NOT NULL COMMENT 'Đường dẫn file và dòng code',
  \`coverage_type\` VARCHAR(50) NOT NULL COMMENT 'Loại bao phủ (Branch Coverage, Statement Coverage, Path Coverage, Boundary Value Analysis)',
  \`description\` TEXT COMMENT 'Mô tả mục tiêu kiểm thử mã nguồn',
  \`test_input\` TEXT COMMENT 'Tham số / Dữ liệu đầu vào',
  \`expected_path\` TEXT COMMENT 'Luồng thực thi mong đợi',
  \`verified_code_line\` TEXT COMMENT 'Dòng code thực tế đã được verify',
  \`status\` VARCHAR(20) DEFAULT 'Đạt' COMMENT 'Kết quả (Đạt / Không đạt)',
  \`notes\` TEXT COMMENT 'Ghi chú đánh giá độ bao phủ mã nguồn',
  \`created_at\` DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (\`id\`),
  KEY \`idx_wb_func\` (\`function_name\`),
  KEY \`idx_wb_type\` (\`coverage_type\`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng lưu kết quả kiểm thử hộp trắng (White Box Testing)';

INSERT INTO \`qa_white_box_tests\` 
(\`id\`, \`function_name\`, \`file_location\`, \`coverage_type\`, \`description\`, \`test_input\`, \`expected_path\`, \`verified_code_line\`, \`status\`, \`notes\`) 
VALUES\n`;

const wbValues = wbRows.map(row => {
    const [id, funcName, fileLoc, covType, desc, testInput, expPath, codeLine, status, notes] = row;
    return `(${escapeSQL(id)}, ${escapeSQL(funcName)}, ${escapeSQL(fileLoc)}, ${escapeSQL(covType)}, ${escapeSQL(desc)}, ${escapeSQL(testInput)}, ${escapeSQL(expPath)}, ${escapeSQL(codeLine)}, ${escapeSQL(status)}, ${escapeSQL(notes)})`;
});

sqlOutput += wbValues.join(',\n') + ';\n\n';

// ------------------------------------------------------------------------------
// Views & QA Analytics Queries
// ------------------------------------------------------------------------------
sqlOutput += `-- ==============================================================================
-- 5. QA ANALYTICS VIEWS & USEFUL REPORTING QUERIES FOR TESTERS
-- ==============================================================================

-- View 1: Thống kê tỷ lệ Pass/Fail theo Module
CREATE OR REPLACE VIEW \`v_qa_module_summary\` AS
SELECT 
    module AS \`Module\`,
    COUNT(*) AS \`Tổng số Test Cases\`,
    SUM(CASE WHEN status = 'Đạt' THEN 1 ELSE 0 END) AS \`Số lượng Đạt\`,
    SUM(CASE WHEN status != 'Đạt' THEN 1 ELSE 0 END) AS \`Số lượng Chưa đạt/Lỗi\`,
    ROUND((SUM(CASE WHEN status = 'Đạt' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) AS \`Tỷ lệ Đạt (%)\`
FROM \`qa_test_cases\`
GROUP BY module
ORDER BY \`Tỷ lệ Đạt (%)\` ASC;

-- View 2: Tổng hợp Bug theo Severity & Priority
CREATE OR REPLACE VIEW \`v_qa_bug_severity_summary\` AS
SELECT 
    severity AS \`Mức độ nghiêm trọng\`,
    priority AS \`Độ ưu tiên\`,
    COUNT(*) AS \`Số lượng Bug\`,
    SUM(CASE WHEN status = 'Đã khắc phục' THEN 1 ELSE 0 END) AS \`Đã khắc phục\`,
    SUM(CASE WHEN status != 'Đã khắc phục' THEN 1 ELSE 0 END) AS \`Còn tồn đọng\`
FROM \`qa_bug_reports\`
GROUP BY severity, priority
ORDER BY \`Số lượng Bug\` DESC;

-- View 3: Tổng hợp độ bao phủ kiểm thử Hộp Trắng (White Box Coverage)
CREATE OR REPLACE VIEW \`v_qa_whitebox_coverage_summary\` AS
SELECT 
    coverage_type AS \`Loại bao phủ mã nguồn\`,
    COUNT(*) AS \`Số lượng hàm được kiểm thử\`,
    SUM(CASE WHEN status = 'Đạt' THEN 1 ELSE 0 END) AS \`Số hàm Pass\`,
    ROUND((SUM(CASE WHEN status = 'Đạt' THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) AS \`Tỷ lệ phủ (%)\`
FROM \`qa_white_box_tests\`
GROUP BY coverage_type;

SET FOREIGN_KEY_CHECKS = 1;

-- ==============================================================================
-- HƯỚNG DẪN SỬ DỤNG CHO TESTER:
-- 1. Import file này vào MySQL Workbench, phpMyAdmin hoặc CLI:
--    mysql -u root -p database_name < tester_database.sql
-- 2. Xem báo cáo tổng quan kết quả kiểm thử:
--    SELECT * FROM v_qa_module_summary;
--    SELECT * FROM v_qa_bug_severity_summary;
--    SELECT * FROM v_qa_whitebox_coverage_summary;
-- ==============================================================================
`;

fs.writeFileSync(path.join(baseDir, 'tester_database.sql'), sqlOutput, 'utf8');
console.log('Created tester_database.sql successfully!');
