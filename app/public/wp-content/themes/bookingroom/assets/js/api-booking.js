document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('api-booking-form');
    const resultsContainer = document.getElementById('api-booking-results');
    const submitBtn = document.getElementById('api-booking-submit');
    const btnText = submitBtn ? submitBtn.querySelector('.btn-text') : null;
    const spinner = submitBtn ? submitBtn.querySelector('.spinner-icon') : null;

    if (!form) return;

    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Ngăn submit form mặc định

        // Lấy dữ liệu từ form
        const checkIn = document.getElementById('booking-check-in').value;
        const checkOut = document.getElementById('booking-check-out').value;
        const adults = form.querySelector('[name="adults"]').value;
        const children = form.querySelector('[name="children"]').value;

        // Validation cơ bản
        if (!checkIn || !checkOut) {
            alert('Vui lòng chọn ngày nhận và trả phòng.');
            return;
        }

        // Hiển thị trạng thái loading
        setLoadingState(true);
        resultsContainer.innerHTML = '';
        resultsContainer.classList.add('hidden');

        // Tạo URL API nội bộ
        const apiUrl = `/wp-json/bookingroom/v1/search?check_in=${encodeURIComponent(checkIn)}&check_out=${encodeURIComponent(checkOut)}&adults=${encodeURIComponent(adults)}&children=${encodeURIComponent(children)}`;

        // Gọi API bằng Fetch
        fetch(apiUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                setLoadingState(false);
                
                if (data.success && data.data) {
                    renderResults(data.data);
                } else {
                    renderError(data.message || 'Không tìm thấy phòng trống trong khoảng thời gian này.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                setLoadingState(false);
                renderError('Đã xảy ra lỗi khi kết nối với hệ thống đặt phòng. Vui lòng thử lại sau.');
            });
    });

    function setLoadingState(isLoading) {
        if (isLoading) {
            if (submitBtn) submitBtn.disabled = true;
            if (btnText) btnText.textContent = 'Đang tìm kiếm...';
            if (spinner) spinner.classList.remove('hidden');
        } else {
            if (submitBtn) submitBtn.disabled = false;
            if (btnText) btnText.textContent = 'Kiểm tra phòng trống';
            if (spinner) spinner.classList.add('hidden');
        }
    }

    function renderError(message) {
        resultsContainer.classList.remove('hidden');
        resultsContainer.innerHTML = `
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-xl">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">${message}</p>
                    </div>
                </div>
            </div>
        `;
    }

    function renderResults(apiData) {
        resultsContainer.classList.remove('hidden');
        
        // Tùy thuộc vào cấu trúc dữ liệu trả về từ 3rd-party API để render html
        // Đây là code HTML mẫu dạng list các phòng
        let html = `
            <h3 class="text-2xl font-bold text-slate-800 mb-6">Kết quả tìm kiếm</h3>
            <div class="space-y-6">
        `;

        if (Array.isArray(apiData) && apiData.length > 0) {
            apiData.forEach(room => {
                html += `
                    <div class="bg-white border border-slate-200 rounded-2xl p-6 flex flex-col md:flex-row gap-6 shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-full md:w-1/3 bg-slate-100 rounded-xl h-48 flex items-center justify-center overflow-hidden">
                            ${room.image ? `<img src="${room.image}" alt="${room.name}" class="w-full h-full object-cover">` : '<svg class="w-12 h-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>'}
                        </div>
                        <div class="w-full md:w-2/3 flex flex-col justify-between">
                            <div>
                                <h4 class="text-xl font-bold text-slate-900 mb-2">${room.name || 'Phòng Tiêu Chuẩn'}</h4>
                                <p class="text-slate-600 text-sm mb-4 line-clamp-2">${room.description || 'Tiện nghi đầy đủ, không gian thoáng đãng.'}</p>
                                
                                <div class="flex items-center gap-4 text-sm text-slate-500">
                                    <span class="flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg> Max ${room.max_occupancy || 2} Khách</span>
                                </div>
                            </div>
                            
                            <div class="mt-6 flex items-end justify-between border-t border-slate-100 pt-4">
                                <div>
                                    <p class="text-sm text-slate-500">Giá từ</p>
                                    <p class="text-2xl font-bold text-blue-600">${formatCurrency(room.price || 1500000)}</p>
                                </div>
                                <button onclick="bookRoom('${room.id || ''}')" class="bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white font-bold py-3 px-8 rounded-xl transition-colors">
                                    Đặt Ngay
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            html += `<p class="text-slate-600">Rất tiếc, không có phòng nào trống trong khoảng thời gian này. Vui lòng thử ngày khác.</p>`;
        }

        html += `</div>`;
        resultsContainer.innerHTML = html;
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
    }
});

// Hàm mẫu để xử lý khi click Đặt Ngay
window.bookRoom = function(roomId) {
    alert('Hành động này sẽ gửi POST request lên /wp-json/bookingroom/v1/book để chốt đơn phòng ' + roomId);
    // Tại đây sẽ thu thập thông tin khách hàng và gọi API POST /book
};
