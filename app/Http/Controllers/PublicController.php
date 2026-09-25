<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Models\Patient;
use Inertia\Inertia;

class PublicController extends Controller
{
    /**
     * Extra presentation data (icon + services) for known departments.
     * Falls back to a generic icon/services list for anything not listed here.
     */
    protected function departmentMeta(): array
    {
        return [
            'Cardiology' => [
                'icon' => 'heart',
                'services' => ['Heart Checkup', 'ECG & Echo', 'Angiography', 'Heart Disease Treatment', 'Preventive Cardiology'],
            ],
            'Pediatrics' => [
                'icon' => 'baby',
                'services' => ['Child Wellness Visits', 'Vaccinations', 'Growth & Nutrition', 'Newborn Care', 'Childhood Illness Treatment'],
            ],
            'Orthopedics' => [
                'icon' => 'bone',
                'services' => ['Joint Replacement', 'Fracture Care', 'Sports Injuries', 'Spine Treatment', 'Physiotherapy Referral'],
            ],
            'Dermatology' => [
                'icon' => 'skin',
                'services' => ['Skin Checkups', 'Acne & Scar Treatment', 'Allergy Testing', 'Cosmetic Dermatology', 'Hair & Scalp Care'],
            ],
            'General Medicine' => [
                'icon' => 'stethoscope',
                'services' => ['General Checkups', 'Chronic Disease Care', 'Fever & Infection Treatment', 'Health Screenings', 'Referrals'],
            ],
            'Gynecology' => [
                'icon' => 'gynecology',
                'services' => ['Prenatal Care', 'Ultrasound', 'Family Planning', 'Women\'s Wellness', 'Postnatal Care'],
            ],
            'Neurology' => [
                'icon' => 'brain',
                'services' => ['Headache & Migraine Care', 'Stroke Management', 'Epilepsy Treatment', 'Nerve Disorders', 'EEG Testing'],
            ],
            'Urology' => [
                'icon' => 'urology',
                'services' => ['Kidney Stone Treatment', 'Urinary Infections', 'Prostate Care', 'Bladder Disorders', 'Minor Procedures'],
            ],
            'ENT' => [
                'icon' => 'ent',
                'services' => ['Ear Infections', 'Sinus & Allergy Care', 'Hearing Tests', 'Throat Disorders', 'Minor ENT Surgery'],
            ],
        ];
    }

    protected function statsOverview(): array
    {
        return [
            'years' => '10+',
            'doctors' => Doctor::count() ?: '50+',
            'patients' => Patient::count()?: '1000+',
            'satisfaction' => '95%',
        ];
    }

    public function home()
    {
        $departments = Department::where('status', true)->take(4)->get();
        $doctors = Doctor::with('department')->take(4)->get();

        return Inertia::render('Public/Home', [
            'departments' => $departments,
            'doctors' => $doctors,
            'stats' => $this->statsOverview(),
            'meta' => $this->departmentMeta(),
        ]);
    }

    public function about()
    {
        return Inertia::render('Public/About', [
            'stats' => $this->statsOverview(),
        ]);
    }

    public function departments()
    {
        $departments = Department::withCount('doctors')->where('status', true)->get();

        return Inertia::render('Public/Departments', [
            'departments' => $departments,
            'meta' => $this->departmentMeta(),
        ]);
    }

    public function departmentShow(Department $department)
    {
        $department->loadCount('doctors');
        $doctors = Doctor::where('department_id', $department->id)->take(4)->get();

        $meta = $this->departmentMeta();
        $info = $meta[$department->name] ?? [
            'icon' => 'stethoscope',
            'services' => ['Consultations', 'Diagnosis', 'Treatment Plans', 'Follow-up Care', 'Preventive Advice'],
        ];

        return Inertia::render('Public/DepartmentDetail', [
            'department' => $department,
            'doctors' => $doctors,
            'icon' => $info['icon'],
            'services' => $info['services'],
        ]);
    }

    public function doctors(Request $request)
    {
        $query = Doctor::with('department');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $doctors = $query->orderBy('name')->paginate(9)->withQueryString();
        $departments = Department::where('status', true)->get();

        return Inertia::render('Public/Doctors', [
            'doctors' => $doctors,
            'departments' => $departments,
            'filters' => $request->only(['search', 'department_id']),
        ]);
    }

    public function doctorShow(Doctor $doctor)
    {
        $doctor->load(['department', 'availabilities' => function ($q) {
            $q->where('is_active', true);
        }]);

        $related = Doctor::where('department_id', $doctor->department_id)
            ->where('id', '!=', $doctor->id)
            ->take(3)
            ->get();

        return Inertia::render('Public/DoctorProfile', [
            'doctor' => $doctor,
            'related' => $related,
        ]);
    }

    public function appointment(Request $request)
    {
        $departments = Department::where('status', true)->get();
        $doctors = Doctor::with('department')->get();

        return Inertia::render('Public/Appointment', [
            'departments' => $departments,
            'doctors' => $doctors,
            'preselect' => [
                'department_id' => $request->query('department_id'),
                'doctor_id' => $request->query('doctor_id'),
            ],
        ]);
    }

    public function appointmentStore(Request $request)
    {
        $validated = $request->validate([
            'doctor_id' => ['required', 'exists:doctors,id'],
            'patient_name' => ['required', 'string', 'max:255'],
            'patient_phone' => ['required', 'string', 'max:30'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'appointment_time' => ['required'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        Appointment::create([
            'doctor_id' => $validated['doctor_id'],
            'patient_name' => $validated['patient_name'],
            'patient_phone' => $validated['patient_phone'],
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'notes' => $validated['notes'] ?? null,
            'status' => 'Pending',
        ]);

        return redirect()->route('public.appointment')->with('success', 'Your appointment request has been received. Our team will contact you shortly to confirm.');
    }

    public function services()
    {
        $services = [
            ['name' => 'General Checkup', 'icon' => 'stethoscope', 'desc' => 'Routine health checkups and screenings for the whole family.'],
            ['name' => 'Emergency Care', 'icon' => 'ambulance', 'desc' => '24/7 emergency response with a dedicated critical-care team.'],
            ['name' => 'Laboratory Services', 'icon' => 'flask', 'desc' => 'Accurate diagnostic testing with fast turnaround times.'],
            ['name' => 'Radiology & Imaging', 'icon' => 'scan', 'desc' => 'X-ray, ultrasound, CT and MRI imaging under one roof.'],
            ['name' => 'Pharmacy', 'icon' => 'pill', 'desc' => 'In-house pharmacy stocked with prescribed medication.'],
            ['name' => 'Surgery', 'icon' => 'scalpel', 'desc' => 'Modern operating theatres for minor and major procedures.'],
            ['name' => 'Maternity Care', 'icon' => 'gynecology', 'desc' => 'Prenatal, delivery and postnatal care for mother and baby.'],
            ['name' => 'ICU Care', 'icon' => 'heart', 'desc' => 'Round-the-clock intensive care with advanced monitoring.'],
            ['name' => 'Physiotherapy', 'icon' => 'bone', 'desc' => 'Rehabilitation programs to restore movement and strength.'],
        ];

        return Inertia::render('Public/Services', [
            'services' => $services,
        ]);
    }

    public function contact()
    {
        return Inertia::render('Public/Contact');
    }

    public function contactStore(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        ContactMessage::create($validated);

        return redirect()->route('public.contact')->with('success', 'Thanks for reaching out! We will get back to you soon.');
    }

    public function gallery()
    {
        $images = [
            ['title' => 'Main Reception', 'category' => 'Facilities'],
            ['title' => 'Operation Theatre', 'category' => 'Facilities'],
            ['title' => 'Patient Room', 'category' => 'Facilities'],
            ['title' => 'ICU Ward', 'category' => 'Facilities'],
            ['title' => 'Cardiology Department', 'category' => 'Departments'],
            ['title' => 'Pediatrics Department', 'category' => 'Departments'],
            ['title' => 'Laboratory', 'category' => 'Departments'],
            ['title' => 'Pharmacy Counter', 'category' => 'Departments'],
            ['title' => 'Health Awareness Camp', 'category' => 'Events'],
            ['title' => 'Free Checkup Drive', 'category' => 'Events'],
            ['title' => 'Blood Donation Day', 'category' => 'Events'],
            ['title' => 'Hospital Anniversary', 'category' => 'Events'],
        ];

        return Inertia::render('Public/Gallery', [
            'images' => $images,
        ]);
    }

    public function blog()
    {
        $posts = [
            [
                'title' => 'Tips for a Healthy Heart',
                'category' => 'Cardiology',
                'date' => 'Mar 10, 2024',
                'excerpt' => 'Simple daily habits that keep your cardiovascular system strong, from diet to exercise and stress management.',
            ],
            [
                'title' => 'Importance of Regular Checkups',
                'category' => 'General',
                'date' => 'Mar 5, 2024',
                'excerpt' => 'Why yearly screenings catch problems early and how often you should really be visiting your doctor.',
            ],
            [
                'title' => 'Child Health and Nutrition',
                'category' => 'Pediatrics',
                'date' => 'Feb 28, 2024',
                'excerpt' => 'A practical guide for parents on building balanced meals and healthy routines for growing children.',
            ],
            [
                'title' => 'How to Maintain Healthy Bones',
                'category' => 'Orthopedics',
                'date' => 'Feb 20, 2024',
                'excerpt' => 'Calcium, vitamin D and the right kind of exercise: what actually keeps your bones strong as you age.',
            ],
        ];

        return Inertia::render('Public/Blog', [
            'posts' => $posts,
        ]);
    }
}