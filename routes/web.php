<?php

use App\Http\Controllers\AcademicCalendarController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\AppointmentsDashboardController;
use App\Http\Controllers\AppointmentsUser;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\CourseListingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DepartmentsController;
use App\Http\Controllers\DeptHeadsController;
use App\Http\Controllers\EnrolledSubjectsController;
use App\Http\Controllers\EnrollmentsController;
use App\Http\Controllers\FacultyRecordsController;
use App\Http\Controllers\FilesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ProgramSubjectController;
use App\Http\Controllers\RegistrarFunctionsController;
use App\Http\Controllers\ScrapingController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SectionSubjectSchedulesController;
use App\Http\Controllers\SectionSubjectsController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\StudentNoteController;
use App\Http\Controllers\StudentRecordsController;
use App\Http\Controllers\SubjectCatalogController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\UserDashboardController;
use App\Livewire\ProgramAndCourseManagement;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->role === 'admin') {
            return redirect('/admin/dashboard'); // Redirect admins to the admin dashboard
        } else {
            return redirect('/user/dashboard'); // Redirect regular users to the user dashboard
        }
    } else {
        return redirect()->route('login'); // Redirect guests to the login page
    }
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');
    /* Appointments */
    Route::get('/user/appointments', [AppointmentsUser::class, 'index'])->name('user.appointments');
    Route::post('/appointments/create-appointment', [AppointmentsController::class, 'request_appointment'])->name('appointments.request');
    /* Public Json */
    Route::get('/public/api/get_services', [ServicesController::class, 'all_services_json'])->name('all_services_json');
    Route::get('/user/pending-requests', [AppointmentsController::class, 'getUserAppointments'])->name('user.pending-requests');
    Route::get('/user/complete-requests', [AppointmentsController::class, 'getUserCompletedAppointments'])->name('user.complete-requests');
});

/* Admin Middleware */
Route::middleware(['auth','isAdminUser'])->group(function() {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    // Route::get('/admin/dashboard', [ChartController::class, 'barChart']);
    Route::get('/admin/dashboard/get-articles', [DashboardController::class, 'scrapedNews'])->name('scraped-news.json');
    Route::get('/admin/dashboard/get-active-classes', [DashboardController::class, 'getActiveClasses'])->name('active-classes.json');
    Route::get('/admin/dashboard/get-calendar-events', [DashboardController::class, 'calendarEvents'])->name('calendar-events.json');
    Route::get('/admin/dashboard/get-daily-quote', [ScrapingController::class, 'scrapeDailyQuotes'])->name('daily-quote.json');

/* Student Records */
    Route::get('/admin/student-records', [StudentRecordsController::class, 'index'])->name('student-records');
    Route::get('/admin/student-records/{student}', [StudentRecordsController::class, 'show'])->name('student-records.show');
    Route::get('/student/student-records/add_student', function () {
        return view('admin.add-student');
    })->name('student.add');
    Route::post('/student/student-records/add_student', [StudentRecordsController::class, 'store'])->name('student.store');
    Route::post('/student/{student_id}/notes', [StudentNoteController::class, 'store'])->name('student-notes.store');
    Route::delete('/student/student-records/delete_student/{student_id}', [StudentRecordsController::class, 'destroy'])->name('student-delete');
    Route::get('/student/student-records/edit/{student}', [StudentRecordsController::class, 'edit'])->name('student.edit');
    Route::patch('/student/student-records/edit/update/{student}', [StudentRecordsController::class, 'update_personal'])->name('student.update');
/* Student File Records */
    Route::post('/students/{studentId}/files/upload', [FilesController::class, 'uploadFile'])->name('student-files.upload');
    Route::get('/students/{studentId}/files/download/{filename}', [FilesController::class, 'download'])->name('student-files.download');
    Route::delete('admin/files/delete/{id}', [FilesController::class, 'destroy'])->name('student-files.delete');
    Route::get('/student-image/{studentId}/{filename}', [FilesController::class, 'getStudentImage'])->name('student.image');
/* Faculty Records */
    Route::get('/admin/faculty-records', [FacultyRecordsController::class, 'index'])->name('faculty-records');
    Route::get('/admin/faculty-records/{faculty}', [FacultyRecordsController::class, 'show'])->name('faculty-records.show');
    Route::post('/admin/faculty-records/create-faculty-record', [FacultyRecordsController::class, 'store'])->name('faculty-records.store');
    Route::patch('/admin/faculty-records/update-faculty-record/{id}', [FacultyRecordsController::class, 'update'])->name('faculty-records.update');
    Route::delete('/admin/faculty-records/delete-faculty-record/{id}', [FacultyRecordsController::class, 'destroy'])->name('faculty-records.destroy');
/* Subject Profile */
    Route::get('/admin/course-listings', [CourseListingsController::class, 'index'])->name('course-listings');
    Route::get('/admin/course-listings/{course}', [CourseListingsController::class, 'show'])->name('course-listings.show');
/* Enrollments */
    Route::get('/admin/enrollment-records', [EnrollmentsController::class, 'index'])->name('enrollment-records');
    Route::get('/admin/enrollment-records/{enrollment_id}', [EnrollmentsController::class, 'show'])->name('enrollment-records.show');
    Route::get('/admin/enrollments/enroll', [EnrollmentsController::class, 'enroll'])->name('enrollments.enroll');
    Route::post('/admin/enrollments/enroll/enroll_student',[EnrollmentsController::class, 'store'])->name('enrollments.store');
/* Enroll Subjects */
    Route::post('/admin/enrollments/enroll/enroll_subjects/{enrollment_id}',[EnrolledSubjectsController::class, 'store'])->name('enroll.subjects');
/* Departments */
    Route::get('/admin/functions/program-course-management/departments', [DepartmentsController::class, 'index'])->name('departments');
    Route::post('/admin/functions/program-course-management/departments/create-dept-record', [DepartmentsController::class, 'store'])->name('departments.store');
    Route::patch('/admin/functions/program-course-management/departments/update-dept-record/{id}', [DepartmentsController::class, 'update'])->name('departments.update');
    Route::delete('/admin/functions/program-course-management/departments/delete-dept-record/{id}', [DepartmentsController::class, 'destroy'])->name('departments.destroy');
/* Dept Heads */    
    Route::post('/admin/functions/program-course-management/departments/create-deptHead-record', [DeptHeadsController::class, 'store'])->name('deptHead.store');
    Route::patch('/admin/functions/program-course-management/departments/update-deptHead-record/{id}', [DeptHeadsController::class, 'update'])->name('deptHead.update');
    Route::delete('/admin/functions/program-course-management/departments/delete-deptHead-record/{id}', [DeptHeadsController::class, 'destroy'])->name('deptHead.destroy');
/* Program Management */
    Route::get('/admin/functions/program-course-management/program_list', [ProgramController::class, 'index'])->name('program-list');
    /* Sub - Program Profile */
    Route::get('/admin/functions/program-course-management/program_list/{program_id}', [ProgramController::class, 'show'])->name('program-list.show');
    Route::get('/admin/functions/program-course-management/program_list/{program_id}/assign_subject', [SubjectController::class, 'search'])->name('program-lists-subjects.search');
    Route::post('/admin/functions/program-course-management/program_list/save-program', [ProgramController::class, 'store'])->name('program-lists-new-program');    
    Route::post('/admin/functions/program-course-management/program_list/{program_id}/save-assign_subject', [ProgramSubjectController::class, 'store'])->name('program-subject.save');
    Route::delete('/admin/functions/program-course-management/program_list/delete-program/{program_id}', [ProgramController::class, 'destroy'])->name('program-lists-delete-program');
    Route::patch('/admin/functions/program-course-management/program_list/update-program/{id}', [ProgramController::class, 'update'])->name('program-lists-update-program');
/* Subject Catalog */
    Route::get('/admin/functions/program-course-management/subject_catalog', [SubjectCatalogController::class, 'index'])->name('subject-catalog');
    Route::post('/admin/functions/program-course-management/subject_catalog/save-subject', [SubjectCatalogController::class, 'store'])->name('subject-catalog-new-subject');
    Route::delete('/admin/functions/program-course-management/subject_catalog/delete/{id}',[SubjectCatalogController::class,'delete'])->name('subject-catalog.delete');
    Route::patch('/admin/functions/program-course-management/subject_catalog/update/{id}',[SubjectCatalogController::class,'update'])->name('subject-catalog.update');
/* Academic Calendar */
    Route::get('/admin/functions/program-course-management/academic_calendar', [AcademicCalendarController::class, 'index'])->name('academic-calendar');
    Route::post('/admin/functions/program-course-management/academic_calendar/add-event', [AcademicCalendarController::class, 'store'])->name('academic-calendar-add-event');
    Route::post('admin/functions/program-course-management/academic_calendar/set-acad-year',[AcademicYearController::class, 'store'])->name('acad-year-set');
    Route::delete('/admin/functions/program-course-management/academic_calendar/delete-event/{id}', [AcademicCalendarController::class, 'destroy'])->name('academic-calendar-delete-event');
/* Academic Year */
    Route::get('/admin/functions/program-course-management/academic_year',[AcademicYearController::class, 'index'])->name('academic-year');
    Route::post('/admin/functions/program-course-management/academic_year/add_acad_year',[AcademicYearController::class, 'store'])->name('academic-year.store');
    Route::patch('/admin/functions/program-course-management/academic_year/update_acad_year/{id}',[AcademicYearController::class, 'update'])->name('academic-year.update');
    Route::delete('/admin/functions/program-course-management/academic_year/delete_acad_year/{id}',[AcademicYearController::class, 'destroy'])->name('academic-year.delete');
/* Class Schedules */
    Route::get('/admin/functions/program-course-management/sections',[SectionController::class, 'index'])->name('sections');
    Route::get('/admin/functions/program-course-management/sections/{id}',[SectionController::class, 'show'])->name('sections.show');
    // Route::get('/admin/functions/program-course-management/sections/create',[SectionController::class, 'create_section'])->name('section.create');
    Route::post('/admin/functions/program-course-management/sections/create',[SectionController::class, 'store'])->name('section.create');
/* Sections, Section Subjects */
    Route::post('/admin/functions/sections/assign-schedule',[SectionSubjectsController::class, 'store'])->name('section-subject.store');
    Route::post('/admin/functions/sections/store-subjects-free',[SectionSubjectsController::class, 'store_free'])->name('section-subject-free.store');
    Route::post('/admin/functions/sections/store-subjects-free/set_schedule',[SectionSubjectSchedulesController::class, 'store_schedule_free_section'])->name('section-subject-free-schedule.store');
/* Appointments */
    Route::get('admin/appointments/dashboard', [AppointmentsController::class, 'index'])->name('appointments.dashboard');
    Route::get('admin/appointments/json', [AppointmentsController::class, 'appointmentsCalendarJson'])->name('appointments.json');
    Route::get('admin/appointments/queue-json', [AppointmentsDashboardController::class, 'appointmentsQueue'])->name('appointments.queue');
    Route::get('admin/appointments/latest-appt-json', [AppointmentsDashboardController::class, 'latestAppointment'])->name('appointments.queue-latest');
    Route::patch('/admin/dashboard/save-appt-mgmt-settings', [AppointmentsDashboardController::class, 'saveMgmtSettings'])->name('appointments.save-mgmt-settings');
    Route::get('admin/appointments/manage/{id}', [AppointmentsController::class, 'manage'])->name('appointments.manage');
    Route::get('admin/appointments/appointments-list', [AppointmentsController::class, 'appointments'])->name('appointments');
    Route::get('admin/appointments/services', [ServicesController::class, 'index'])->name('appointments.services');
    Route::post('admin/appointments/services/create', [ServicesController::class, 'store'])->name('appointments.create-service');
    Route::patch('admin/appointments/services/update/{service_id}', [ServicesController::class, 'update'])->name('appointments.update');
    Route::delete('admin/appointments/services/delete/{service_id}', [ServicesController::class, 'delete'])->name('appointments.delete');
/* Local APIs */
    Route::get('/admin/students/get-students/', [StudentRecordsController::class, 'student_json'])->name('students.json');
    Route::get('/admin/students/get-students/{student_id}', [StudentRecordsController::class, 'fetch_student_json'])->name('students.fetch');
    Route::get('/admin/functions/get-subjects', [SubjectController::class, 'search'])->name('gimme-subjects');
    Route::get('/admin/functions/get-subjects/{subject_id}', [SubjectCatalogController::class, 'fetch_subject'])->name('subject.fetch');
    Route::get('/admin/functions/get-programs', [ProgramController::class, 'program_json'])->name('program.jsonn');
    Route::get('/admin/functions/get-programs/{program_id}', [ProgramController::class, 'fetch_program_json'])->name('program.json');
    // Route::get('/admin/functions/get-program-subjects/', [ProgramSubjectController::class, 'program_subjects_json'])->name('program-subjects.json');
    Route::get('/admin/functions/get-program-subjects/', [ProgramSubjectController::class, 'fetchProgramSubjects'])->name('program-subjects.json');
    Route::get('/admin/functions/get-program-subjects/all', [ProgramSubjectController::class, 'program_subjects_json'])->name('program-subjects-all.json');
    Route::get('/program/{program_id}/subjects/{year}/{term}', [ProgramSubjectController::class, 'fetchSubjects'])->name('fetch.subjects');
    Route::get('/sections/fetch', [SectionController::class,'fetchSections'])->name('fetch.sections');
    Route::get('/admin/functions/get-faculty', [FacultyRecordsController::class, 'faculty_json'])->name('faculty_json');
    Route::get('/admin/functions/faculty/search', [FacultyRecordsController::class, 'searchFaculty'])->name('faculty.search');
    Route::get('/admin/functions/get-faculty/{prof_id}', [FacultyRecordsController::class, 'fetch_faculty_json'])->name('faculty_json.fetch');
    Route::get('/admin/functions/get-schedules', [SectionSubjectSchedulesController::class, 'sec_sub_schedule_json'])->name('sec_sub_schedule_json');
    Route::get('/admin/functions/fetch-schedule', [SectionSubjectSchedulesController::class, 'fetchScheduleDetailsForSectionAndSubject'])->name('sec_sub_schedule.fetch');
    Route::get('/admin/functions/get-section-subjects', [SectionSubjectsController::class, 'search'])->name('sec_sub.search');
});

require __DIR__.'/auth.php';
