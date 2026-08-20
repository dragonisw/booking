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

const baseDir = __dirname;
const tcContent = fs.readFileSync(path.join(baseDir, 'TestCases_BookingRoom.csv'), 'utf8');
const bugContent = fs.readFileSync(path.join(baseDir, 'BugReport_BookingRoom.csv'), 'utf8');
const wbContent = fs.readFileSync(path.join(baseDir, 'WhiteBoxTesting_BookingRoom.csv'), 'utf8');

const tcRows = parseCSV(tcContent);
const bugRows = parseCSV(bugContent);
const wbRows = parseCSV(wbContent);

console.log('Test Cases parsed:', tcRows.length - 1);
console.log('Bug Reports parsed:', bugRows.length - 1);
console.log('White Box Tests parsed:', wbRows.length - 1);
