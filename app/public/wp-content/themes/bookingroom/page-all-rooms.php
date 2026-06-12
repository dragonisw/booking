<?php
/*
Template Name: Tất cả phòng (Giao diện mới)
*/
get_header(); ?>

<!-- Thêm tạm Tailwind CDN để render các class mới mà không cần build lại style.css -->
<script src="https://cdn.tailwindcss.com"></script>

<main class="bg-slate-50 min-h-screen pb-24">
    <!-- Hero Section -->
    <section class="relative py-32 bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 overflow-hidden">
        <!-- Đã xóa thẻ img tải từ Unsplash để tránh lỗi broken image / nhiễu màn hình -->
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="container mx-auto px-4 relative z-10 text-center">
            <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-6 drop-shadow-lg tracking-tight">
                Khám Phá Các Hạng Phòng
            </h1>
            <p class="text-lg md:text-xl text-slate-200 max-w-2xl mx-auto font-light leading-relaxed mb-8">
                Trải nghiệm không gian nghỉ dưỡng đẳng cấp với tiện nghi hiện đại và thiết kế tinh tế.
            </p>
            <nav class="flex justify-center items-center text-slate-300 text-sm gap-3">
                <a href="<?php echo home_url(); ?>" class="hover:text-white transition-colors">Trang chủ</a>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                <span class="text-white font-medium">Tất cả phòng</span>
            </nav>
        </div>
    </section>

    <!-- Rooms Grid Section -->
    <section class="container mx-auto px-4 -mt-16 relative z-20">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            <?php
            $args = array(
                'post_type' => 'room',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'orderby' => 'menu_order',
                'order' => 'ASC'
            );
            $rooms_query = new WP_Query($args);

            if ($rooms_query->have_posts()) :
                while ($rooms_query->have_posts()) : $rooms_query->the_post();
                    $price = get_post_meta(get_the_ID(), '_price', true) ?: 1500000;
                    $capacity = get_post_meta(get_the_ID(), '_capacity', true) ?: '2 Khách';
                    $size = get_post_meta(get_the_ID(), '_size', true) ?: '35m²';
                    $bed_type = get_post_meta(get_the_ID(), '_bed_type', true) ?: '1 Giường đôi';
                    $terms = get_the_terms(get_the_ID(), 'room_category');
            ?>
                <!-- Room Card -->
                <article class="bg-white rounded-[2.5rem] overflow-hidden shadow-xl shadow-slate-200/50 hover:shadow-2xl hover:shadow-blue-900/10 transition-all duration-500 group flex flex-col border border-slate-100/50 relative">
                    
                    <!-- Image Wrapper -->
                    <div class="relative h-80 overflow-hidden">
                        <a href="<?php the_permalink(); ?>" class="block h-full w-full">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('large', array('class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-in-out')); ?>
                            <?php else : ?>
                                <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=800" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700 ease-in-out" alt="Room Default">
                            <?php endif; ?>
                        </a>
                        
                        <!-- Top Badges -->
                        <div class="absolute top-6 left-6 flex flex-wrap gap-2">
                            <?php if ($terms) : foreach ($terms as $term) : ?>
                                <span class="bg-white/90 backdrop-blur-md text-slate-800 text-[11px] uppercase font-bold px-4 py-2 rounded-2xl shadow-lg">
                                    <?php echo esc_html($term->name); ?>
                                </span>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-8 flex flex-col flex-grow">
                        <div class="flex justify-between items-start mb-4">
                            <h2 class="text-2xl font-bold text-slate-900 leading-tight">
                                <a href="<?php the_permalink(); ?>" class="hover:text-blue-600 transition-colors">
                                    <?php the_title(); ?>
                                </a>
                            </h2>
                        </div>
                        
                        <p class="text-slate-500 text-sm mb-6 line-clamp-2 leading-relaxed">
                            <?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?>
                        </p>

                        <!-- Amenities -->
                        <div class="grid grid-cols-2 gap-y-4 gap-x-2 mb-8 mt-auto">
                            <div class="flex items-center text-slate-600 text-sm">
                                <svg class="w-5 h-5 mr-2.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                <?php echo esc_html($capacity); ?>
                            </div>
                            <div class="flex items-center text-slate-600 text-sm">
                                <svg class="w-5 h-5 mr-2.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                <?php echo esc_html($size); ?>
                            </div>
                            <div class="flex items-center text-slate-600 text-sm col-span-2">
                                <svg class="w-5 h-5 mr-2.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                                <?php echo esc_html($bed_type); ?>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                            <div>
                                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block mb-1">Giá từ</span>
                                <div class="flex items-baseline">
                                    <span class="text-2xl font-extrabold text-blue-600"><?php echo number_format($price); ?>đ</span>
                                    <span class="text-slate-500 text-sm ml-1">/ đêm</span>
                                </div>
                            </div>
                            <a href="<?php the_permalink(); ?>" class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all duration-300 group-hover:scale-110">
                                <svg class="w-5 h-5 transform -rotate-45" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </article>
            <?php 
                endwhile;
                wp_reset_postdata();
            else :
            ?>
                <div class="col-span-full bg-white rounded-3xl p-12 text-center shadow-sm">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-100 mb-6">
                        <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-2">Chưa có phòng nào</h3>
                    <p class="text-slate-500 max-w-md mx-auto">Hiện tại chúng tôi đang cập nhật danh sách phòng. Vui lòng quay lại sau nhé!</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php get_footer(); ?>
