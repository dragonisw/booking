document.addEventListener('DOMContentLoaded', function() {
    // Các elements của form Đặt Phòng (page-booking.php)
    const bookingForm = document.getElementById('api-booking-form');
    
    // Các elements của form Tìm Phòng (page-tim-phong.php)
    const availForm = document.getElementById('br-avail-form');
    
    // Các elements kết quả chung
    let resultsContainer = document.getElementById('api-booking-results') || document.getElementById('br-avail-results');
    let skeletonLoader = document.getElementById('br-avail-loading'); // Có ở page-tim-phong.php

    // Xử lý form ở page-booking.php
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('api-booking-submit');
            const btnText = submitBtn ? submitBtn.querySelector('.btn-text') : null;
            const spinner = submitBtn ? submitBtn.querySelector('.spinner-icon') : null;

            const checkIn = document.getElementById('booking-check-in').value;
            const checkOut = document.getElementById('booking-check-out').value;
            const adults = bookingForm.querySelector('[name="adults"]').value;
            const children = bookingForm.querySelector('[name="children"]').value;

            if (!checkIn || !checkOut) {
                alert('Vui lòng chọn ngày nhận và trả phòng.');
                return;
            }

            // Trạng thái loading cho button
            if (submitBtn) submitBtn.disabled = true;
            if (btnText) btnText.textContent = 'Đang tìm kiếm...';
            if (spinner) spinner.classList.remove('hidden');

            fetchRoomData(checkIn, checkOut, adults, children, function() {
                if (submitBtn) submitBtn.disabled = false;
                if (btnText) btnText.textContent = 'Kiểm tra phòng trống';
                if (spinner) spinner.classList.add('hidden');
            });
        });
    }

    // Xử lý form ở page-tim-phong.php
    if (availForm) {
        availForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('br-avail-submit-btn');
            
            const checkIn = document.getElementById('br_check_in').value;
            const checkOut = document.getElementById('br_check_out').value;
            const adults = document.getElementById('br_adults').value;
            const children = document.getElementById('br_children').value;
            const roomType = document.getElementById('br_room_type').value;

            if (!checkIn || !checkOut) {
                alert('Vui lòng chọn ngày nhận và trả phòng.');
                return;
            }

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-70', 'cursor-not-allowed');
            }

            fetchRoomData(checkIn, checkOut, adults, children, function() {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
                }
            }, roomType);
        });
    }

    // Hàm gọi API chung
    function fetchRoomData(checkIn, checkOut, adults, children, callback, roomType = '') {
        // Hiển thị trạng thái Skeleton loading
        if (resultsContainer) {
            resultsContainer.innerHTML = '';
            resultsContainer.classList.add('hidden');
        }
        if (skeletonLoader) {
            skeletonLoader.classList.remove('hidden');
        }

        const apiUrl = `/wp-json/bookingroom/v1/search?check_in=${encodeURIComponent(checkIn)}&check_out=${encodeURIComponent(checkOut)}&adults=${encodeURIComponent(adults)}&children=${encodeURIComponent(children)}&room_type=${encodeURIComponent(roomType)}`;

        fetch(apiUrl)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                if (skeletonLoader) skeletonLoader.classList.add('hidden');
                
                if (data.success && data.data) {
                    renderResults(data.data, checkIn, checkOut);
                } else {
                    renderError(data.message || 'Không tìm thấy phòng trống trong khoảng thời gian này.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (skeletonLoader) skeletonLoader.classList.add('hidden');
                renderError('Đã xảy ra lỗi khi kết nối với hệ thống đặt phòng. Vui lòng thử lại sau.');
            })
            .finally(() => {
                if (callback) callback();
            });
    }

    function renderError(message) {
        if (!resultsContainer) return;
        resultsContainer.classList.remove('hidden');
        resultsContainer.innerHTML = `
            <div class="text-center py-20 bg-white rounded-3xl border-2 border-dashed border-red-200">
                <svg class="w-16 h-16 mx-auto text-red-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <h3 class="text-xl font-bold text-slate-900 mb-2">Thông báo</h3>
                <p class="text-slate-500">${message}</p>
            </div>
        `;
    }

    function renderResults(apiData, checkIn, checkOut) {
        if (!resultsContainer) return;
        resultsContainer.classList.remove('hidden');
        
        let html = '';

        if (Array.isArray(apiData) && apiData.length > 0) {
            html += `<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">`;
            
            apiData.forEach(room => {
                html += `
                    <div class="bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl border-2 border-slate-100 hover:border-orange-300 transition-all duration-300 group flex flex-col">
                        <div class="relative h-52 overflow-hidden bg-slate-200">
                            ${room.image ? `<img src="${room.image}" alt="${room.name}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">` : ''}
                            
                            <div class="absolute top-3 right-3">
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">
                                    ✓ Có phòng
                                </span>
                            </div>
                            
                            <div class="absolute bottom-3 right-3 bg-white/95 backdrop-blur rounded-xl px-3 py-2 shadow">
                                <div class="text-[#d35400] font-black text-sm">${formatCurrency(room.price || 0)}</div>
                                <div class="text-slate-400 text-[9px] font-medium">/ đêm</div>
                            </div>
                        </div>
                        
                        <div class="p-6 flex flex-col flex-1 gap-4">
                            <div>
                                <h3 class="text-xl font-bold text-slate-900 mb-2 group-hover:text-[#d35400] transition-colors">${room.name || 'Phòng'}</h3>
                                <p class="text-slate-500 text-sm leading-relaxed line-clamp-2">${room.description || ''}</p>
                            </div>
                            
                            <div class="mt-auto pt-4 border-t border-slate-100">
                                <button onclick="bookRoom('${room.id || ''}', '${checkIn}', '${checkOut}')" class="block w-full text-center bg-[#d35400] hover:bg-[#b84300] text-white font-bold py-3.5 rounded-2xl transition-all shadow-md hover:shadow-lg hover:shadow-orange-200/50">
                                    Đặt phòng ngay →
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += `</div>`;
        } else {
            renderError('Rất tiếc, không có phòng nào trống trong khoảng thời gian này. Vui lòng thử ngày khác.');
            return;
        }

        resultsContainer.innerHTML = html;
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount).replace('₫', 'đ');
    }
});

window.bookRoom = function(roomId, checkIn, checkOut) {
    alert('Bắt đầu quy trình đặt phòng cho mã phòng: ' + roomId + '\\nTừ ' + checkIn + ' đến ' + checkOut);
    // Tại đây gọi API POST /book
};
