// =====================================================================
// YAHAN APNI IMAGES ADD KARO
// 1) Apni image ko  resources/js/images/  folder me rakho
// 2) Neeche upar import likho, phir list me ek line add kar do
// Category sirf: 'Facilities' | 'Departments' | 'Events'
// (Online link bhi chalta hai:  image: 'https://....jpg' )
// =====================================================================
import building from '@/images/hospital-building.avif';
import waitingArea from '@/images/gallery/waitingArea.avif';
import rooms from '@/images/gallery/room.avif';
import ward from '@/images/gallery/ward.avif';
import coridoor from '@/images/gallery/coridoor.avif';
import icu from '@/images/icu.avif';
import lab from '@/images/lab.avif';
import emergency from '@/images/emergency.avif';
import ot from '@/images/ot.webp';
import cardiology from '@/images/cardiology.png';
import pediatrics from '@/images/pediatrics.png';
import neurology from '@/images/neurology.jpg';
import event1 from '@/images/gallery/events1.jpg';
import event2 from '@/images/gallery/events2.jpg';



// Gallery page + Home ke 4 cards (Home par pehli 4 dikhti hain)
export const galleryImages = [
    { title: 'Hospital Building', category: 'Facilities', image: building },
    { title: 'ICU Ward', category: 'Facilities', image: icu },
    { title: 'Operation Theatre', category: 'Facilities', image: ot },
    { title: 'Emergency Unit', category: 'Facilities', image: emergency },
    { title: 'Waiting Area', category: 'Facilities', image: waitingArea },
    { title: 'Rooms', category: 'Facilities', image: rooms },
    { title: 'Hall Ward', category: 'Facilities', image: ward },
    { title: 'CooriDoor', category: 'Facilities', image: coridoor},
    { title: 'Laboratory', category: 'Departments', image: lab },
    { title: 'Cardiology Department', category: 'Departments', image: cardiology },
    { title: 'Pediatrics Department', category: 'Departments', image: pediatrics },
    { title: 'Neurology Department', category: 'Departments', image: neurology },
    { title: 'Free Checkup Drive', category: 'Events', image: event1 },
    { title: 'Meeting', category: 'Events', image: event2 },

];