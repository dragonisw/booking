<?php
/*
Template Name: Tất cả phòng (Giao diện mới)
*/
get_header(); ?>

<!-- Thêm tạm Tailwind CDN để render các class mới mà không cần build lại style.css -->
<script src="https://cdn.tailwindcss.com"></script>

<style>
    /* Custom styles for the Poshanu-inspired layout */
    .room-title-serif {
        font-family: 'Playfair Display', Georgia, serif;
    }
</style>

<main class="bg-white min-h-screen pb-24">
    <!-- Header Spacing -->
    <div class="pt-10 pb-16 text-center">
        <h1 class="text-3xl md:text-5xl font-bold text-orange-600 uppercase tracking-widest room-title-serif mb-4">
            PHÒNG NGHỈ
        </h1>
        <div class="w-16 h-0.5 bg-orange-500 mx-auto"></div>
    </div>

    <!-- Rooms List Section -->
    <section class="container mx-auto px-4 max-w-6xl">
        <div class="space-y-24 md:space-y-32">
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
                    $capacity = get_post_meta(get_the_ID(), '_capacity', true) ?: '2 người';
                    $size = get_post_meta(get_the_ID(), '_size', true) ?: '35m²';
                    $bed_type = get_post_meta(get_the_ID(), '_bed_type', true) ?: '1 Giường đôi';
                    $view = get_post_meta(get_the_ID(), '_view', true) ?: 'Hướng vườn';
                    $terms = get_the_terms(get_the_ID(), 'room_category');
            ?>
                <!-- Single Room Row -->
                <article class="room-row">
                    <!-- Main Image (Full width of container) -->
                    <div class="relative w-full h-[300px] md:h-[450px] lg:h-[550px] overflow-hidden group">
                        <a href="<?php the_permalink(); ?>" class="block w-full h-full">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('1536x1536', array('class' => 'w-full h-full object-cover group-hover:scale-105 transition-transform duration-700')); ?>
                            <?php else : ?>
                                <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" alt="<?php the_title(); ?>">
                            <?php endif; ?>
                        </a>
                    </div>
                    
                    <!-- Content Overlapping Area -->
                    <div class="flex flex-col lg:flex-row relative z-10 -mt-10 md:-mt-20 lg:-mt-24 mx-4 md:mx-8 lg:mx-0">
                        
                        <!-- Left Overlapping White Box -->
                        <div class="lg:w-5/12 bg-white shadow-[0_10px_40px_rgba(0,0,0,0.08)] p-6 md:p-8 lg:p-10 lg:ml-12 border-t-[3px] border-orange-500 text-center flex flex-col items-center">
                            
                            <!-- Thumbnails (Fake slider look) -->
                            <div class="flex gap-2 justify-center mb-6">
                                <?php 
                                // Show featured image as thumbnail 1, and placeholder as 2
                                $thumb_url = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') ?: 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=150&q=80';
                                ?>
                                <img src="<?php echo esc_url($thumb_url); ?>" class="w-20 h-14 md:w-24 md:h-16 object-cover border border-slate-200" alt="thumb">
                                <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=150&q=80" class="w-20 h-14 md:w-24 md:h-16 object-cover border border-slate-200" alt="thumb">
                            </div>

                            <!-- Title -->
                            <h2 class="text-2xl md:text-3xl font-bold text-[#e65c00] uppercase mb-4 tracking-wider room-title-serif">
                                <a href="<?php the_permalink(); ?>" class="hover:text-orange-400 transition-colors">
                                    <?php the_title(); ?>
                                </a>
                            </h2>

                            <!-- Meta Info Icons -->
                            <div class="flex flex-wrap justify-center items-center gap-3 md:gap-5 text-xs md:text-sm text-slate-500">
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <?php echo esc_html($view); ?>
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                                    <?php echo esc_html($size); ?>
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <?php echo esc_html($capacity); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Right Description and Buttons -->
                        <div class="lg:w-7/12 pt-8 lg:pt-32 lg:pl-16 pb-4">
                            <p class="text-slate-600 leading-relaxed text-sm md:text-base mb-8">
                                <?php 
                                $excerpt = get_the_excerpt();
                                echo !empty($excerpt) ? wp_trim_words($excerpt, 40, '...') : 'Mỗi phòng đều được thiết kế hiện đại và tiện nghi sang trọng, được bố trí thành không gian riêng biệt là phòng ngủ, phòng khách, và phòng tắm. Phòng có cửa sổ rộng, không gian thoáng đãng mang lại trải nghiệm nghỉ dưỡng hoàn hảo.';
                                ?>
                            </p>
                            
                            <!-- Action Buttons -->
                            <div class="flex flex-wrap items-center gap-4">
                                <a href="<?php the_permalink(); ?>" class="px-6 py-2.5 border border-slate-300 text-slate-500 hover:border-[#e65c00] hover:text-[#e65c00] transition-colors uppercase text-xs font-bold tracking-widest bg-white">
                                    XEM CHI TIẾT <span class="ml-1">></span>
                                </a>
                                <a href="<?php the_permalink(); ?>?booking=1" class="px-8 py-2.5 border border-[#e65c00] text-[#e65c00] hover:bg-[#e65c00] hover:text-white transition-colors uppercase text-xs font-bold tracking-widest flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    BOOKING
                                </a>
                            </div>
                        </div>

                    </div>
                </article>
            <?php 
                endwhile;
                wp_reset_postdata();
            else :
            ?>
                <div class="text-center p-12 text-slate-500">
                    Chưa có phòng nào được cập nhật.
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php get_footer(); ?>
