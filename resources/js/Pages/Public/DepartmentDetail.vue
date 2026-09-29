<script setup>
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Icon from '@/Components/Public/Icon.vue';
import Avatar from '@/Components/Public/Avatar.vue';

defineProps({
    department: { type: Object, required: true },
    doctors: { type: Array, default: () => [] },
    icon: { type: String, default: 'stethoscope' },
    services: { type: Array, default: () => [] },
});
</script>

<template>
  <PublicLayout>
       <!-- Hero Section -->
      <section class="relative bg-slate-900 overflow-hidden h-56 sm:h-64 flex items-center">

         <div v-if="department.image_url" class="absolute inset-0 z-0">
             <img :src="department.image_url":alt="department.name" class="w-full h-full object-cover object-center"/>

             <div class="absolute inset-0 bg-slate-900/40"></div>
         </div>

          <div v-else class="absolute inset-0 bg-gradient-to-r from-brand-900 to-slate-900 z-0"></div>

           <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
               <div class="max-w-xl pl-4 sm:pl-8 lg:pl-12">
                   <h1 class="font-heading font-extrabold text-4xl sm:text-4xl text-white tracking-tight drop-shadow-md">
                     {{ department.name }}
                   </h1>
                  <p class="mt-2 text-white/80 text-lg sm:text-base font-medium drop-shadow">
                     {{ department.description
                     ? department.description.substring(0, 80) + '...' : `Advanced ${department.name} Care for a Healthier Life` }}
                 </p>
             </div>
          </div> 
      </section>

        <!-- Main Section -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 grid lg:grid-cols-2 gap-12">
            <div>
                <div class="aspect-video rounded-2xl overflow-hidden bg-gradient-to-br from-brand-100 to-brand-200 flex items-center justify-center mb-8 shadow-sm">
                    <img v-if="department.image_url" :src="department.image_url" :alt="department.name"
                        class="w-full h-full object-cover" />
                    <Icon v-else :name="icon" class="w-20 h-20 text-brand-400" />
                </div>
                <h2 class="font-heading font-bold text-2xl text-slate-900 mb-3">{{ department.name }}</h2>
                <p class="text-slate-500 leading-relaxed">
                    {{ department.description || `Our ${department.name} team combines experienced specialists with modern diagnostic and treatment technology to deliver the best possible outcomes for every patient.` }}
                </p>
            </div>

            <div>
                <h3 class="font-heading font-bold text-xl text-slate-900 mb-5">Services We Offer</h3>
                <ul class="space-y-3 mb-10">
                    <li v-for="s in services" :key="s" class="flex items-center gap-3 text-sm text-slate-600">
                        <span class="w-6 h-6 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                            <Icon name="check" class="w-3.5 h-3.5" />
                        </span>
                        {{ s }}
                    </li>
                </ul>

                <h3 class="font-heading font-bold text-xl text-slate-900 mb-5">Our {{ department.name }} Specialists</h3>
                <div v-if="doctors.length" class="grid sm:grid-cols-2 gap-4 mb-8">
                    <div v-for="doc in doctors" :key="doc.id" class="bg-white rounded-xl p-4 border border-slate-100 flex items-center gap-3 shadow-sm">
                        <Avatar :name="doc.name" :photo-url="doc.photo_url" size="md" />
                        <div class="min-w-0">
                            <p class="font-heading font-bold text-sm text-slate-900 truncate">Dr. {{ doc.name }}</p>
                            <p class="text-xs text-slate-400">{{ doc.experience_years ? doc.experience_years + '+ Years Experience' : doc.specialization }}</p>
                            <Link :href="`/our-doctors/${doc.id}`" class="text-xs font-bold text-brand-600 mt-1 inline-block">View Profile</Link>
                        </div>
                    </div>
                </div>
                <p v-else class="text-sm text-slate-400 mb-8">Specialists for this department will be listed here soon.</p>

                <div class="rounded-2xl bg-brand-50 p-6 text-center">
                    <h4 class="font-heading font-bold text-slate-900 mb-2">Book Appointment</h4>
                    <p class="text-sm text-slate-500 mb-4">Get the best medical care from our {{ department.name }} specialists.</p>
                    <Link :href="`/book-appointment?department_id=${department.id}`"
                        class="inline-flex items-center gap-2 bg-brand-600 text-white font-bold px-6 py-3 rounded-xl hover:bg-brand-700 transition">
                        Book Appointment <Icon name="arrow-right" class="w-4 h-4" />
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>