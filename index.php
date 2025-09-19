<?php 
    $db_host = 'localhost'; $db_user = 'root'; $db_pass = ''; $db_name = 'cw_landing_page';
    try { $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass); $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); } catch (PDOException $e) { die("Database Error."); }
    try { $settings = $pdo->query("SELECT name, value FROM settings")->fetchAll(PDO::FETCH_KEY_PAIR); } catch (PDOException $e) { $settings = []; }
    try { $sections_visibility = $pdo->query("SELECT name, is_enabled FROM sections")->fetchAll(PDO::FETCH_KEY_PAIR); } catch (PDOException $e) { $sections_visibility = []; }
    function getContent($pdo, $section, $element) { try { $stmt = $pdo->prepare("SELECT value FROM content WHERE section = :section AND element = :element"); $stmt->execute(['section' => $section, 'element' => $element]); return htmlspecialchars($stmt->fetchColumn() ?: ''); } catch (PDOException $e) { return ''; } }
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title><?php echo htmlspecialchars(isset($settings['site_title']) ? $settings['site_title'] : 'Welcome'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <style>
        :root {
	--primary-color: <?php echo htmlspecialchars(isset($settings['primary_color']) ? $settings['primary_color'] : '#4f46e5');
	?>;
	--secondary-color: <?php echo htmlspecialchars(isset($settings['secondary_color']) ? $settings['secondary_color'] : '#7c3aed');
	?>;
}

body {
	font-family: 'Inter', sans-serif;
}

.reveal {
	opacity: 0;
	transform: translateY(30px);
	transition: opacity .8s ease-out, transform .8s ease-out
}

.reveal.visible {
	opacity: 1;
	transform: translateY(0)
}

.hero-gradient {
	background: linear-gradient(-45deg, var(--primary-color), var(--secondary-color), #0ea5e9, #14b8a6);
	background-size: 400% 400%;
	animation: gradientBG 15s ease infinite
}

@keyframes gradientBG {
	0% {
		background-position: 0 50%
	}

	50% {
		background-position: 100% 50%
	}

	100% {
		background-position: 0 50%
	}
}

.bg-primary {
	background-color: var(--primary-color)
}

.text-primary {
	color: var(--primary-color)
}

.hover\:bg-primary-dark:hover {
	filter: brightness(.9)
}

#video-modal {
	opacity: 0;
	visibility: hidden;
	transition: opacity .3s ease, visibility .3s ease
}

.video-modal-active #video-modal {
	opacity: 1;
	visibility: visible
}

.hero-slider {
	height: 90vh
}

.swiper-slide {
	background-size: cover;
	background-position: center
}

.swiper-pagination-bullet-active {
	background-color: var(--primary-color) !important
}

.swiper-button-next,
.swiper-button-prev {
	color: #fff !important
}

.faq-question {
	cursor: pointer
}

.faq-answer {
	max-height: 0;
	overflow: hidden;
	transition: max-height .5s ease-in-out
}

.faq-item.active .faq-answer {
	max-height: 200px
}

.faq-item.active .faq-icon {
	transform: rotate(180deg)
}

.faq-icon {
	transition: transform .3s ease-in-out
}

#scroll-to-top {
	position: fixed;
	bottom: 2rem;
	right: 2rem;
	background-color: var(--primary-color);
	color: #fff;
	width: 50px;
	height: 50px;
	border-radius: 50%;
	display: none;
	align-items: center;
	justify-content: center;
	cursor: pointer;
	z-index: 1000;
	opacity: 0;
	transition: opacity .3s, visibility .3s;
	box-shadow: 0 4px 12px rgba(0, 0, 0, .2)
}

#scroll-to-top.visible {
	display: flex;
	opacity: 1
}

/* Style for the typing cursor */
.typed-cursor {
	font-size: 3.75rem;
	color: white;
}
    </style>
</head>
<body class="text-gray-800">

    <header id="header" class="bg-white shadow-md fixed w-full z-40 top-0 transition-all duration-300">
        <nav class="container mx-auto px-6 py-3 flex justify-between items-center">
            <a href="#"><img src="<?php echo htmlspecialchars(isset($settings['logo_url']) ? $settings['logo_url'] : ''); ?>" alt="Logo" class="h-10"></a>
            <div class="hidden md:flex space-x-8 items-center"><a href="#services" class="text-gray-600 hover:text-primary">Services</a><a href="#about" class="text-gray-600 hover:text-primary">About</a><a href="#contact" class="bg-primary text-white px-5 py-2 rounded-full hover:bg-primary-dark shadow-lg">Contact Us</a></div>
            <button id="mobile-menu-button" class="md:hidden text-gray-700"><i class="fas fa-bars fa-2x"></i></button>
        </nav>
        <div id="mobile-menu" class="hidden md:hidden bg-white py-4"><a href="#services" class="block text-center py-2">Services</a><a href="#about" class="block text-center py-2">About</a><a href="#contact" class="block text-center py-2">Contact</a></div>
    </header>

    <main>
        <?php $hero_type = isset($settings['hero_type']) ? $settings['hero_type'] : 'gradient'; if ($hero_type == 'slider'): $slides_stmt = $pdo->query("SELECT * FROM hero_slides ORDER BY sort_order ASC"); $slides = $slides_stmt->fetchAll(PDO::FETCH_ASSOC); ?>
            <section id="hero-slider" class="swiper hero-slider">
    <?php if (!empty($slides)):?>
        <div class="swiper-wrapper">
            <?php foreach ($slides as $slide): ?>
                <div class="swiper-slide" style="background-image: url('<?php echo htmlspecialchars($slide['image_url']); ?>')">
                    <div class="w-full h-full flex items-center justify-center bg-black bg-opacity-50 text-white text-center p-4">
                        <div>
                            <h1 class="text-4xl md:text-6xl font-extrabold"><?php echo htmlspecialchars($slide['headline']); ?></h1>
                            <p class="text-lg md:text-xl my-4">
                                <?php echo htmlspecialchars($slide['tagline']); ?>
                            </p><a href="#contact" class="bg-primary text-white font-bold py-3 px-8 rounded-full">Get Started</a></div>
                    </div>
                </div>
                <?php endforeach; ?>
        </div>
        <div class="swiper-pagination"></div>
        <div class="swiper-button-prev"></div>
        <div class="swiper-button-next"></div>
        <?php endif; ?>
</section>
        <?php else: ?>
            <section id="hero" class="hero-gradient text-white pt-32 pb-20">
                <div class="container mx-auto px-6 text-center">
                    <!-- MODIFIED: Headline structure for typing animation -->
                    <h1 class="text-4xl md:text-6xl font-extrabold leading-tight mb-4 reveal">
                        Build Your Future With Our <br/> <span id="typed-text-element"></span>
                    </h1>
                    <p class="text-lg md:text-xl mb-8 max-w-3xl mx-auto reveal"><?php echo getContent($pdo, 'hero', 'tagline'); ?></p>
                    <a href="#services" class="bg-white text-primary font-bold py-3 px-8 rounded-full text-lg">Get Started</a>
                </div>
            </section>
        <?php endif; ?>

        <?php if (isset($sections_visibility['services']) && $sections_visibility['services']): ?><section id="services" class="py-20 bg-white"><div class="container mx-auto px-6"><div class="text-center mb-12"><h2 class="text-3xl md:text-4xl font-bold reveal">Our Services</h2></div><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"><?php $services_stmt=$pdo->query("SELECT * FROM services ORDER BY id LIMIT 3");foreach($services_stmt->fetchAll(PDO::FETCH_ASSOC) as $i=>$s){echo "<div class='bg-gray-50 p-8 rounded-xl shadow-lg reveal' style='transition-delay:".($i*200)."ms;'><div class='text-4xl text-primary mb-4'><i class='".htmlspecialchars($s['icon_class'])."'></i></div><h3 class='text-xl font-bold mb-2'>".htmlspecialchars($s['title'])."</h3><p class='text-gray-600'>".htmlspecialchars($s['description'])."</p></div>";}?></div></div></section><?php endif; ?>
        <?php if (isset($sections_visibility['about']) && $sections_visibility['about']): ?><section id="about" class="py-20"><div class="container mx-auto px-6"><div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center"><div class="reveal"><img src="https://placehold.co/600x400/<?php echo substr(isset($settings['primary_color']) ? $settings['primary_color'] : '4f46e5', 1); ?>/ffffff?text=Our+Team" alt="About Us" class="rounded-xl shadow-2xl w-full"></div><div class="reveal"><h2 class="text-3xl md:text-4xl font-bold mb-4">About Our Company</h2><p class="text-gray-600 mb-4 leading-relaxed"><?php echo nl2br(getContent($pdo, 'about', 'paragraph1')); ?></p><a href="#contact" class="bg-primary text-white font-bold py-3 px-6 rounded-full">Learn More</a></div></div></div></section><?php endif; ?>
        <?php if (isset($sections_visibility['features']) && $sections_visibility['features']): ?><section id="features" class="py-20 bg-white"><div class="container mx-auto px-6"><div class="text-center mb-12"><h2 class="text-3xl md:text-4xl font-bold reveal">Why Choose Us?</h2></div><div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 text-center"><div class="reveal"><div class="bg-indigo-100 text-primary w-20 h-20 mx-auto rounded-full flex items-center justify-center text-3xl mb-4"><i class="fas fa-rocket"></i></div><h3 class="text-xl font-bold mb-2">Fast Delivery</h3></div><div class="reveal" style="transition-delay:200ms"><div class="bg-indigo-100 text-primary w-20 h-20 mx-auto rounded-full flex items-center justify-center text-3xl mb-4"><i class="fas fa-headset"></i></div><h3 class="text-xl font-bold mb-2">24/7 Support</h3></div><div class="reveal" style="transition-delay:400ms"><div class="bg-indigo-100 text-primary w-20 h-20 mx-auto rounded-full flex items-center justify-center text-3xl mb-4"><i class="fas fa-shield-alt"></i></div><h3 class="text-xl font-bold mb-2">Secure & Reliable</h3></div><div class="reveal" style="transition-delay:600ms"><div class="bg-indigo-100 text-primary w-20 h-20 mx-auto rounded-full flex items-center justify-center text-3xl mb-4"><i class="fas fa-thumbs-up"></i></div><h3 class="text-xl font-bold mb-2">Proven Results</h3></div></div></div></section><?php endif; ?>
        <?php if (isset($sections_visibility['cta']) && $sections_visibility['cta']): ?><section id="cta" class="py-20 bg-primary text-white"><div class="container mx-auto px-6 text-center"><h2 class="text-3xl md:text-4xl font-bold mb-4 reveal">Ready to Start Your Project?</h2><a href="#contact" class="bg-white text-primary font-bold py-3 px-8 rounded-full text-lg">Get a Free Quote</a></div></section><?php endif; ?>
        <?php if (isset($sections_visibility['video']) && $sections_visibility['video']): ?><section id="video-section" class="py-20"><div class="container mx-auto px-6 text-center"><div class="text-center mb-12"><h2 class="text-3xl md:text-4xl font-bold reveal">See Our Work in Action</h2></div><div id="video-thumbnail-container" class="relative max-w-4xl mx-auto reveal cursor-pointer group"><img src="https://placehold.co/1280x720/1e293b/ffffff?text=Click+to+Play" alt="Video Thumbnail" class="rounded-xl shadow-2xl w-full"><div class="absolute inset-0 flex items-center justify-center"><div class="bg-white/30 backdrop-blur-sm w-24 h-24 rounded-full flex items-center justify-center text-white text-5xl group-hover:scale-110 transition-all"><i class="fas fa-play"></i></div></div></div></div></section><?php endif; ?>
        <?php if (isset($sections_visibility['testimonials']) && $sections_visibility['testimonials']): ?><section id="testimonials" class="py-20 bg-white"><div class="container mx-auto px-6"><div class="text-center mb-12"><h2 class="text-3xl md:text-4xl font-bold reveal">What Our Clients Say</h2></div><div class="grid grid-cols-1 md:grid-cols-3 gap-8"><?php $testimonials_stmt=$pdo->query("SELECT * FROM testimonials ORDER BY id LIMIT 3");foreach($testimonials_stmt->fetchAll(PDO::FETCH_ASSOC) as $i=>$t){echo "<div class='bg-gray-50 p-8 rounded-xl shadow-lg reveal' style='transition-delay:".($i*200)."ms;'><div class='flex items-center mb-4'><img src='".htmlspecialchars($t['image_url'])."' alt='".htmlspecialchars($t['client_name'])."' class='h-16 w-16 rounded-full object-cover mr-4'><div><h4 class='font-bold text-lg'>".htmlspecialchars($t['client_name'])."</h4><p class='text-gray-500'>".htmlspecialchars($t['client_position'])."</p></div></div><p class='text-gray-600 italic'>\"".htmlspecialchars($t['quote'])."\"</p></div>";}?></div></div></section><?php endif; ?>
        <?php if (isset($sections_visibility['faq']) && $sections_visibility['faq']): ?><section id="faq" class="py-20"><div class="container mx-auto px-6"><div class="text-center mb-12"><h2 class="text-3xl md:text-4xl font-bold reveal">Frequently Asked Questions</h2></div><div class="max-w-3xl mx-auto"><?php $faq_stmt=$pdo->query("SELECT * FROM faqs ORDER BY id");foreach($faq_stmt->fetchAll(PDO::FETCH_ASSOC) as $i=>$f){echo "<div class='faq-item border-b py-4 reveal' style='transition-delay:".($i*150)."ms;'><div class='faq-question flex justify-between items-center'><h3 class='text-lg font-semibold'>".htmlspecialchars($f['question'])."</h3><span class='faq-icon text-primary text-xl'><i class='fas fa-chevron-down'></i></span></div><div class='faq-answer text-gray-600 pt-2'><p>".htmlspecialchars($f['answer'])."</p></div></div>";}?></div></div></section><?php endif; ?>
        <?php if (isset($sections_visibility['ending-soon']) && $sections_visibility['ending-soon']): ?><section id="ending-soon" class="py-20 bg-amber-400"><div class="container mx-auto px-6 text-center"><h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-2 reveal">Limited Time Offer!</h2><p class="text-lg text-gray-700 mb-8 reveal">Get 20% off on all service packages.</p><div id="countdown" class="flex justify-center space-x-4 md:space-x-8 text-gray-800 mb-8 reveal"><div class="text-center bg-white/30 p-4 rounded-lg w-24"><div id="days" class="text-4xl font-bold">00</div><div class="text-sm uppercase">Days</div></div><div class="text-center bg-white/30 p-4 rounded-lg w-24"><div id="hours" class="text-4xl font-bold">00</div><div class="text-sm uppercase">Hours</div></div><div class="text-center bg-white/30 p-4 rounded-lg w-24"><div id="minutes" class="text-4xl font-bold">00</div><div class="text-sm uppercase">Minutes</div></div><div class="text-center bg-white/30 p-4 rounded-lg w-24"><div id="seconds" class="text-4xl font-bold">00</div><div class="text-sm uppercase">Seconds</div></div></div><a href="#contact" class="bg-gray-800 text-white font-bold py-3 px-8 rounded-full text-lg">Claim Discount</a></div></section><?php endif; ?>
        <?php if (isset($sections_visibility['contact']) && $sections_visibility['contact']): ?><section id="contact" class="py-20 bg-white"><div class="container mx-auto px-6"><div class="text-center mb-12"><h2 class="text-3xl md:text-4xl font-bold reveal">Get in Touch</h2></div><div class="max-w-2xl mx-auto bg-gray-50 p-8 rounded-xl shadow-lg reveal"><div id="form-container"><form id="contact-form"><?php include 'contact_form.php'; ?></form></div></div></div></section><?php endif; ?>
    </main>

    <footer class="bg-gray-800 text-white py-12">
        <div class="container mx-auto px-6"><div class="grid grid-cols-1 md:grid-cols-4 gap-8"><div class="reveal"><h3 class="text-xl font-bold mb-4"><?php echo htmlspecialchars(isset($settings['site_title']) ? $settings['site_title'] : ''); ?></h3></div><div class="reveal"><h3 class="text-lg font-semibold mb-4">Quick Links</h3><ul><li><a href="#about">About</a></li><li><a href="#services">Services</a></li><li><a href="#faq">FAQ</a></li></ul></div><div class="reveal"><h3 class="text-lg font-semibold mb-4">Follow Us</h3><div class="flex space-x-4"><a href="<?php echo htmlspecialchars(isset($settings['facebook_url'])?$settings['facebook_url']:'#'); ?>"><i class="fab fa-facebook-f"></i></a><a href="<?php echo htmlspecialchars(isset($settings['twitter_url'])?$settings['twitter_url']:'#'); ?>"><i class="fab fa-twitter"></i></a><a href="<?php echo htmlspecialchars(isset($settings['linkedin_url'])?$settings['linkedin_url']:'#'); ?>"><i class="fab fa-linkedin-in"></i></a><a href="<?php echo htmlspecialchars(isset($settings['instagram_url'])?$settings['instagram_url']:'#'); ?>"><i class="fab fa-instagram"></i></a></div></div><div class="reveal"><h3 class="text-lg font-semibold mb-4">Newsletter</h3><form><div class="flex"><input type="email" placeholder="Your Email" class="w-full px-4 py-2 rounded-l-lg text-gray-800"><button type="submit" class="bg-primary px-4 py-2 rounded-r-lg"><i class="fas fa-paper-plane"></i></button></div></form></div></div><div class="border-t border-gray-700 mt-8 pt-6 text-center text-gray-500"><p>&copy; <?php echo date("Y"); ?> <?php echo htmlspecialchars(isset($settings['site_title']) ? $settings['site_title'] : ''); ?>. All Rights Reserved.</p></div></div>
    </footer>
    
    <div id="video-modal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center p-4 z-50"><div class="bg-black w-full max-w-4xl aspect-video relative"><button id="close-video-modal" class="absolute -top-10 right-0 text-white text-3xl">&times;</button><iframe id="video-player" class="w-full h-full" src="<?php echo htmlspecialchars(isset($settings['youtube_url'])?$settings['youtube_url']:'');?>?enablejsapi=1" allowfullscreen></iframe></div></div>
    <button id="scroll-to-top" title="Go to top"><i class="fas fa-arrow-up"></i></button>
    
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <!-- ADDED: Typed.js for the typing animation -->
    <script src="https://unpkg.com/typed.js@2.0.16/dist/typed.umd.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded',function(){
            // Your existing JS code...
            new Swiper('.hero-slider',{loop:!0,pagination:{el:'.swiper-pagination',clickable:!0},navigation:{nextEl:'.swiper-button-next',prevEl:'.swiper-button-prev'},autoplay:{delay:5e3,disableOnInteraction:!1}});
            
            // NEW: Typed.js Initialization
            const typedEl = document.getElementById('typed-text-element');
            if (typedEl) {
                var typed = new Typed('#typed-text-element', {
                    strings: ['Expertise.', 'Innovation.', 'Solutions.', 'Success.'],
                    typeSpeed: 50,
                    backSpeed: 30,
                    backDelay: 1500,
                    loop: true,
                    smartBackspace: true
                });
            }

            // Other JS code from previous steps...
            const e=document.getElementById('mobile-menu-button'),t=document.getElementById('mobile-menu');e.addEventListener('click',()=>{t.classList.toggle('hidden')});const o=document.querySelectorAll('.reveal'),s=new IntersectionObserver(e=>{e.forEach(e=>{e.isIntersecting&&e.target.classList.add('visible')})},{threshold:.1});o.forEach(e=>{s.observe(e)});const a=document.querySelectorAll('.faq-item');a.forEach(e=>{e.querySelector('.faq-question').addEventListener('click',()=>{const t=document.querySelector('.faq-item.active');t&&t!==e&&t.classList.remove('active'),e.classList.toggle('active')})});const i=document.getElementById('contact_number'),d=document.getElementById('whatsapp_number'),r=document.getElementById('same_as_contact');r.addEventListener('change',function(){d.value=this.checked?i.value:'',d.readOnly=this.checked}),i.addEventListener('input',function(){r.checked&&(d.value=this.value)});const c=document.getElementById('contact-form'),l=document.getElementById('form-container');c.addEventListener('submit',function(e){e.preventDefault();fetch('contact.php',{method:'POST',body:new FormData(this)}).then(e=>e.json()).then(e=>{e.success&&(l.innerHTML=`<div class="bg-green-100 p-8 rounded-xl text-center"><strong class="font-bold text-2xl">${e.message}</strong><p class="mt-2">Click to chat.</p><a href="${e.whatsapp_link}" target="_blank" class="mt-4 inline-block bg-green-500 text-white font-bold py-3 px-8 rounded-full"><i class="fab fa-whatsapp"></i> Chat Now</a></div>`)})});const u=new Date;u.setDate(u.getDate()+15);setInterval(function(){const e=(new Date).getTime(),t=u-e;document.getElementById("days").innerText=Math.floor(t/864e5).toString().padStart(2,'0'),document.getElementById("hours").innerText=Math.floor(t%864e5/36e5).toString().padStart(2,'0'),document.getElementById("minutes").innerText=Math.floor(t%36e5/6e4).toString().padStart(2,'0'),document.getElementById("seconds").innerText=Math.floor(t%6e4/1e3).toString().padStart(2,'0')},1e3);const p=document.getElementById("video-modal"),y=document.getElementById("video-player"),g=document.getElementById("video-thumbnail-container"),b=document.getElementById("close-video-modal");function f(e,t){y.contentWindow.postMessage(JSON.stringify({event:"command",func:e,args:t||[]}),"*")}g.addEventListener("click",()=>{document.body.classList.add("video-modal-active"),f("playVideo")}),b.addEventListener("click",()=>{document.body.classList.remove("video-modal-active"),f("pauseVideo")});const h=document.getElementById("scroll-to-top");window.addEventListener("scroll",()=>{window.pageYOffset>300?h.classList.add("visible"):h.classList.remove("visible")}),h.addEventListener("click",()=>{window.scrollTo({top:0,behavior:"smooth"})});
        });
    </script>
</body>
</html>
