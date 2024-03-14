@extends('admin.records')
@section('content')
<div>
    <x-alert-message />
    <!-- <a href="{{ route('student-records') }}" class="text-sm text-gray-800 leading-tight no-underline hover:underline">
        <span>Go Back: Student Records</span>
    </a> -->
    <div class="py-2">
        <div class="overflow-x-auto bg-white rounded-lg overflow-y-auto relative px-1" style="min-height: 405px;">   
            <div>
                <form action="{{ route('student.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div class="flex w-full justify-center bg-blue-500 py-1 rounded-md shadow text-white">
                        <h3 class="hover:cursor-default">Student Information</h3>
                    </div>
                    <div class="flex justify-between">
                        <div class="w-4/12">
                            <label for="first_name" class="block text-sm font-medium text-gray-700">First Name:</label>
                            <input type="text" id="first_name" name="first_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="w-3/12">
                            <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name:</label>
                            <input type="text" id="middle_name" name="middle_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="w-3/12">
                            <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name:</label>
                            <input type="text" id="last_name" name="last_name" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="w-1/12">
                            <label for="suffix" class="block text-sm font-medium text-gray-700">Suffix:</label>
                            <select name="suffix" id="suffix" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="" selected></option>
                                <option value="Jr.">Jr.</option>
                                <option value="Sr.">Sr.</option>
                                <option value="II">II</option>
                                <option value="III">III</option>
                                <option value="IV">IV</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-start">
                        <div class="w-4/12 mr-6">
                            <label for="student_number" class="block text-sm font-medium text-gray-700">Student Number:</label>
                            <input type="text" id="student_number" name="student_number" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="w-4/12 mr-6">
                            <label for="phone_number" class="block text-sm font-medium text-gray-700">Contact Number:</label>
                            <input type="text" id="phone_number" name="phone_number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="w-3/12 flex flex-col justify-around">
                            <div class="flex items-center">
                                <label for="is_transferee" class="mr-2 block text-sm font-medium text-gray-700">Transferee:</label>
                                <input type="checkbox" name="is_transferee" id="is_transferee">
                            </div>
                            <div class="flex items-center">
                                <label for="is_irregular" class="mr-2 block text-sm font-medium text-gray-700">Irregular:</label>
                                <input type="checkbox" name="is_irregular" id="is_irregular">
                            </div>
                        </div>  
                    </div>
                    <div class="flex justify-start">
                        <div class="w-6/12 mr-6">
                            <label for="personal_email" class="block text-sm font-medium text-gray-700">Personal Email Address:</label>
                            <input type="text" id="personal_email" name="personal_email" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="w-6/12 mr-6">
                            <label for="school_email" class="block text-sm font-medium text-gray-700">School Email Address:</label>
                            <input type="text" id="school_email" name="school_email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>                      
                    </div>
                    <!-- Personal Information -->
                    <div class="flex w-full justify-center bg-blue-500 py-1 rounded-md shadow text-white">
                        <h3 class="hover:cursor-default">Personal Information</h3>
                    </div>
                    <div class="flex justify-start">
                        <div class="w-4/12 mr-6">
                            <label for="birthdate" class="block text-sm font-medium text-gray-700">Date of Birth:</label>
                            <input type="date" id="birthdate" name="birthdate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="w-4/12 mr-6">
                            <label for="birthplace" class="block text-sm font-medium text-gray-700">Place of Birth:</label>
                            <input type="text" id="birthplace" name="birthplace" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="w-1/12">
                            <label for="sex" class="block text-sm font-medium text-gray-700">Sex:</label>
                            <select name="sex" id="sex" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="" selected></option>
                                <option value="M">M</option>
                                <option value="F">F</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-start">
                        <div class="w-4/12 mr-6">
                            <label for="nationality" class="block text-sm font-medium text-gray-700">Nationality:</label>
                            <input type="text" id="nationality" name="nationality" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="w-4/12 mr-6">
                            <label for="civil_status" class="block text-sm font-medium text-gray-700">Civil Status:</label>
                            <select name="civil_status" id="civil_status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="" selected>Select Marital Status</option>
                                <option value="Single">Single</option>
                                <option value="Married">Married</option>
                                <option value="Widowed">Widowed</option>
                                <option value="Divorced">Divorced</option>
                                <option value="Separated">Separated</option>
                            </select>
                        </div>
                        <div class="w-4/12 mr-6">
                            <label for="religion" class="block text-sm font-medium text-gray-700">Religion:</label>
                            <input type="text" id="religion" name="religion" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                    <!-- Home Address -->
                    <div class="flex w-full justify-center bg-blue-500 py-1 rounded-md shadow text-white">
                        <h3 class="hover:cursor-default">Home Address</h3>
                    </div>
                    <div class="flex">
                        <div class="w-1/12 mr-6">
                            <label for="house_num" class="block text-sm font-medium text-gray-700">House No:</label>
                            <input type="text" id="house_num" name="house_num" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="w-5/12 mr-6">
                            <label for="street" class="block text-sm font-medium text-gray-700">Street Name:</label>
                            <input type="text" id="street" name="street" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="w-5/12">
                            <label for="brgy" class="block text-sm font-medium text-gray-700">Barangay:</label>
                            <input type="text" id="brgy" name="brgy" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                    <div class="flex justify-between">
                        <div class="w-5/12">
                            <label for="city_municipality" class="block text-sm font-medium text-gray-700">City/Municipality:</label>
                            <input type="text" id="city_municipality" name="city_municipality" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="w-5/12">
                            <label for="province" class="block text-sm font-medium text-gray-700">Province:</label>
                            <input type="text" id="province" name="province" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="w-1/12">
                            <label for="zipcode" class="block text-sm font-medium text-gray-700">Zipcode:</label>
                            <input type="text" id="zipcode" name="zipcode" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                    <!-- Educational Background -->
                    <div class="flex w-full justify-center bg-blue-500 py-1 rounded-md shadow text-white">
                        <h3 class="hover:cursor-default">Educational Background</h3>
                    </div>
                    <div class="flex justify-start">
                        <div class="w-6/12 mr-9">
                            <label for="elementary" class="block text-sm font-medium text-gray-700">Primary:</label>
                            <input type="text" id="elementary" name="elementary" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="w-2/12">
                            <label for="elem_yr_grad" class="block text-sm font-medium text-gray-700">Year Graduated:</label>
                            <select name="elem_yr_grad" id="elem_yr_grad" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="" selected>Select Year</option>
                                @for ($year = date('Y'); $year >= date('Y') - 40; $year--)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-start">
                        <div class="w-6/12 mr-9">
                            <label for="jr_highschool" class="block text-sm font-medium text-gray-700">Junior HighSchool:</label>
                            <input type="text" id="jr_highschool" name="jr_highschool" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="w-2/12">
                            <label for="jr_hs_yr_grad" class="block text-sm font-medium text-gray-700">Year Graduated:</label>
                            <select name="jr_hs_yr_grad" id="jr_hs_yr_grad" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="" selected>Select Year</option>
                                @for ($year = date('Y'); $year >= date('Y') - 40; $year--)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-start">
                        <div class="w-6/12 mr-9">
                            <label for="sr_highschool" class="block text-sm font-medium text-gray-700">Senior HighSchool:</label>
                            <input type="text" id="sr_highschool" name="sr_highschool" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="w-2/12">
                            <label for="sr_hs_yr_grad" class="block text-sm font-medium text-gray-700">Year Graduated:</label>
                            <select name="sr_hs_yr_grad" id="sr_hs_yr_grad" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="" selected>Select Year</option>
                                @for ($year = date('Y'); $year >= date('Y') - 40; $year--)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-start">
                        <div class="w-6/12 mr-9">
                            <label for="college" class="block text-sm font-medium text-gray-700">College:</label>
                            <input type="text" id="college" name="college" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="w-2/12">
                            <label for="college_year_ended" class="block text-sm font-medium text-gray-700">Year Ended:</label>
                            <select name="college_year_ended" id="college_year_ended" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="" selected>Select Year</option>
                                @for ($year = date('Y'); $year >= date('Y') - 40; $year--)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <!-- Guardian Information -->
                    <div class="flex w-full justify-center bg-blue-500 py-1 rounded-md shadow text-white">
                        <h3 class="hover:cursor-default">Guardian Information</h3>
                    </div>
                    <div class="flex justify-start">
                        <div class="w-5/12 mr-6">
                            <label for="guardian_name" class="block text-sm font-medium text-gray-700">Guardian Name:</label>
                            <input type="text" id="guardian_name" name="guardian_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div class="w-5/12 mr-6">
                            <label for="guardian_contact" class="block text-sm font-medium text-gray-700">Contact No.:</label>
                            <input type="text" id="guardian_contact" name="guardian_contact" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                    <!-- Submit Button -->
                    <div class="flex w-full justify-center py-6">
                        <button type="submit" class="w-1/2 bg-green-500 font-medium font-semibold text-white px-8 py-2 shadow-md rounded-lg hover:bg-green-600 transition ease-in-out duration-150">Add Student</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection