<script setup>
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Icon from '@/Components/Public/Icon.vue';
import Avatar from '@/Components/Public/Avatar.vue';
import heroBg from '@/images/herobgd.png';
import heroDoc from '@/images/herodoc.png';

const props = defineProps({
    departments: { type: Array, default: () => [] },
    doctors: { type: Array, default: () => [] },
    stats: { type: Object, default: () => ({}) },
    meta: { type: Object, default: () => ({}) },
});

const highlights = [
    { icon: 'award', title: 'Expert Doctors', desc: 'Experienced specialists' },
    { icon: 'building', title: 'Modern Facilities', desc: 'State-of-the-art care' },
    { icon: 'ambulance', title: '24/7 Emergency', desc: 'Always here for you' },
    { icon: 'calendar', title: 'Online Booking', desc: 'Book in seconds' },
];

const news = [
    { title: 'Tips for a Healthy Heart', date: 'Mar 10, 2024',image:'https://plus.unsplash.com/premium_photo-1726837239315-8f00466be229?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8ODF8fG5ld3MlMjBob3NwaXRhbCUyMGltYWdlc3xlbnwwfHwwfHx8MA%3D%3D' },
    { title: 'Importance of Regular Checkups', date: 'Mar 5, 2024',image:'https://plus.unsplash.com/premium_photo-1682089159103-d09b46d1cce8?w=500&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTA4fHxuZXdzJTIwaG9zcGl0YWwlMjBpbWFnZXN8ZW58MHx8MHx8fDA%3D' },
    { title: 'Child Health and Nutrition', date: 'Feb 28, 2024',image:'https://media.istockphoto.com/id/475105054/photo/she-loves-eat-fresh-fruit.webp?a=1&b=1&s=612x612&w=0&k=20&c=A2G3xf9dS5pX5UoHb94GJVpRQhcdKSMEth4j6bylllg=' },
];

const iconFor = (name) => props.meta?.[name]?.icon || 'stethoscope';
</script>

<template>
    <PublicLayout>
        <!-- Hero Section with Background Clinic Image -->
  <section class="relative w-full bg-white overflow-hidden pt-4 pb-6">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Banner Container -->
                <div class="relative  overflow-hidden shadow-sm">
                    <img 
                        :src="heroBg" 
                        alt="Hero Banner" 
                        class="w-full h-[300px] sm:h-[460px] lg:h-[500px] object-cover object-center"
                    />

                    <!-- Content Overlay on Left Side -->
                    <div class="absolute inset-0 flex items-center px-8 sm:px-12 lg:px-28">
                        <div class="max-w-xl space-y-5">
                            <h1 class="font-heading font-extrabold text-4xl sm:text-5xl lg:text-6xl text-slate-900 tracking-tight leading-[1.1]">
                                Your Health<br />
                                <span class="text-teal-600">Our Priority</span>
                            </h1>
                            <p class="text-slate-600 text-sm sm:text-base max-w-md leading-relaxed font-medium">
                                Providing quality healthcare with modern facilities and experienced medical professionals.
                            </p>
                            <div class="flex flex-wrap items-center gap-3 pt-2">
                                <Link href="/book-appointment"
                                    class="inline-flex items-center gap-2 bg-[#0d9488] text-white font-bold px-6 py-3 rounded-xl hover:bg-[#0f766e] transition shadow-md text-sm">
                                    Book Appointment
                                </Link>
                                <Link href="/about"
                                    class="inline-flex items-center gap-2 bg-white/90 backdrop-blur text-slate-700 font-bold px-6 py-3 rounded-xl border border-slate-200 hover:bg-white transition text-sm shadow-sm">
                                    Learn More
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Feature Strip (Alag-alag individual cards/divs ke sath) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 mb-10">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
                <div v-for="h in highlights" :key="h.title" class="bg-gray-100 p-5 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md transition-all flex flex-col items-center gap-4">
                    <span class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center shrink-0">
                        <Icon :name="h.icon" class="w-6 h-6" />
                    </span>
                    <div class="min-w-0 flex flex-col  items-center">
                        <p class="text-sm font-bold text-slate-900 truncate">{{ h.title }}</p>
                        <p class="text-xs text-slate-400 truncate mt-1">{{ h.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Departments -->
          <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header with Title & View All Link -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-[#0F3557]">Our Departments</h2>
            <p class="text-sm text-slate-500 mt-1">Explore our specialized medical departments and expert care.</p>
        </div>
        <Link href="/our-departments" class="text-sm font-bold text-slate-500 hover:text-teal-600 transition-all flex items-center gap-1">
            View All &rarr;
        </Link>
    </div>

    <!-- Cards Grid -->
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <Link v-for="d in departments" :key="d.id" :href="`/our-departments/${d.id}`"
            class="group relative rounded-2xl overflow-hidden border border-slate-100 bg-white shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
            
            <!-- Top Half: Full Image (object-left so right side is protected from cutting) -->
            <div class="h-48 relative overflow-hidden bg-slate-100">
                <template v-if="d.image_url">
                    <img :src="d.image_url" :alt="d.name" class="w-full h-full object-cover object-right group-hover:scale-110 transition-transform duration-500" />
                    <!-- Subtly gradient overlay taake image aur khoobsurat lagay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/60 via-transparent to-transparent opacity-50 group-hover:opacity-30 transition-opacity"></div>
                </template>
                <template v-else>
                    <div class="w-full h-full bg-gradient-to-br from-teal-50 to-blue-50 flex items-center justify-center text-teal-600">
                        <Icon :name="iconFor(d.name)" class="w-16 h-16" />
                    </div>
                </template>
            </div>

            <!-- Bottom Half: Content Area -->
            <div class="p-5 bg-white flex-1 flex flex-col justify-between">
                <div>
                    <h3 class="font-heading font-bold text-[#0f172a] text-lg mb-1 group-hover:text-teal-600 transition-colors">{{ d.name }}</h3>
                    <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed">{{ d.description || 'Specialized medical care and advanced treatments.' }}</p>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-50 flex items-center justify-between text-xs font-semibold text-teal-600">
                    <span>Explore Department</span>
                    <span class="transform group-hover:translate-x-1 transition-transform">&rarr;</span>
                </div>
            </div>
        </Link>
    </div>
</section>

        <!-- Doctors -->
      <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Header with Title & View All Link -->
    <div class="flex items-center justify-between mb-8">
        <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-[#0F3557]">Our Doctors</h2>
        <Link href="/our-doctors" class="text-sm font-bold text-slate-500 hover:text-teal-600 transition-all flex items-center gap-1">
            View All &rarr;
        </Link>
    </div>

    <!-- Cards Grid (4 Columns matching your reference image) -->
    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div v-for="doc in doctors" :key="doc.id"
            class="rounded-3xl overflow-hidden border border-slate-100 bg-white shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col">

            <div class="h-56 w-full bg-slate-100 overflow-hidden relative">
                <template v-if="doc.photo_url">
                    <img :src="doc.photo_url" :alt="doc.name" class="w-full h-full object-cover object-top" />
                </template>
                <template v-else>
                    <div class="w-full h-full flex items-center justify-center text-slate-400">
                        <Avatar :name="doc.name" size="lg" />
                    </div>
                </template>
            </div>

            <div class="p-5 text-center bg-white flex-1 flex flex-col justify-between">
                <div>
                    <h3 class="font-heading font-bold text-[#0F3557] text-base mb-0.5">Dr. {{ doc.name }}</h3>
                    <p class="text-sm text-slate-400 font-medium mb-5">{{ doc.department?.name || doc.specialization || 'Specialist' }}</p>
                </div>

                <Link :href="`/our-doctors/${doc.id}`"
                    class="w-full text-xs font-bold bg-[#014d88] hover:bg-[#013b6d] text-white py-2.5 rounded-full transition-all shadow-sm text-center block tracking-wide">
                    View Profile
                </Link>
            </div>
        </div>
    </div>
</section>

        <!-- CTA -->
       <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
          <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-[#0b314a] to-[#0d9488] shadow-lg">

           <div class="flex items-center min-h-[150px]">

              <div class="w-28 sm:w-36 md:w-44 h-[150px] shrink-0">
                <img :src="heroDoc"alt="Doctor Consultation" class="w-full h-full object-cover object-top"/>
              </div>
             <div class="flex-1 px-5 sm:px-7">
                <h3 class="font-heading font-extrabold text-lg sm:text-xl md:text-2xl text-white leading-tight">
                    Book Your Appointment Today
                </h3>
                <p class="text-teal-100 text-xs sm:text-sm mt-1">
                    Get the best medical care from our experienced doctors.
                </p>
            </div>
            <div class="pr-5 sm:pr-7">
                <Link
                    href="/book-appointment"
                    class="inline-flex items-center justify-center bg-white text-[#0b314a] font-bold px-5 py-4 rounded-xl hover:bg-slate-100 transition shadow-md text-xs sm:text-sm whitespace-nowrap">
                    Book Appointment
                </Link>
            </div>
           </div>
          </div>
      </section>

        <!-- Latest News -->
     <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 mb-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <h2 class="font-heading font-extrabold text-2xl sm:text-3xl text-[#0f172a]">Latest News</h2>
            <Link href="/blog" class="text-sm font-bold text-slate-500 hover:text-teal-600 transition-all flex items-center gap-1">
                View All &rarr;
            </Link>
        </div>

        <!-- News Cards Grid -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <Link v-for="n in news" :key="n.id" href="/blog"
                class="rounded-3xl p-4 border border-slate-100 hover:shadow-xl hover:shadow-slate-100 transition-all group bg-white flex items-center gap-4">
                
                <!-- Left Side: Har news card ke liye uski apni random/static image -->
                <div class="w-28 h-28 rounded-2xl overflow-hidden shrink-0 bg-slate-100">
                    <img 
                        :src="n.image" 
                        :alt="n.title" 
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform" 
                    />
                </div>

                <!-- Right Side: Content -->
                <div class="flex-1 min-w-0 py-1">
                    <p class="text-[11px] text-slate-400 font-semibold mb-1">{{ n.date }}</p>
                    <h3 class="font-heading font-bold text-[#0f172a] text-sm sm:text-base leading-snug line-clamp-2 group-hover:text-teal-600 transition-colors mb-2">
                        {{ n.title }}
                    </h3>
                    <span class="inline-flex items-center gap-1 text-xs font-bold text-teal-600">
                        Read More &rarr;
                    </span>
                </div>
            </Link>
        </div>
    </section>
    </PublicLayout>
</template>