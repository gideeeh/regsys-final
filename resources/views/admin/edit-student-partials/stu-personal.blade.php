<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Student Information') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update student's information.") }}
        </p>
    </header>

    <form method="post" action="{{ route('student.update', ['student' => $student->student_id]) }}" class="mt-6 space-y-6">
        @csrf
        @method('PATCH')
        <div class="flex justify-between">
            <div class="w-4/12">
                <label for="first_name" class="block text-sm font-medium text-gray-700">First Name:</label>
                <x-text-input id="first_name" name="first_name" type="text" class="w-3/12 mt-1 block w-full" :value="old('first_name', $student->first_name)"/>
                <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
            </div>
            <div class="w-3/12">
                <label for="middle_name" class="block text-sm font-medium text-gray-700">Middle Name:</label>
                <x-text-input id="middle_name" name="middle_name" type="text" class="w-3/12 mt-1 block w-full" :value="old('middle_name', $student->middle_name)"/>
                <x-input-error class="mt-2" :messages="$errors->get('middle_name')" />
            </div>
            <div class="w-3/12">
                <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name:</label>
                <x-text-input id="last_name" name="last_name" type="text" class="w-3/12 mt-1 block w-full" :value="old('last_name', $student->last_name)"/>
                <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
            </div>
            <div class="w-1/12">
                <label for="suffix" class="block text-sm font-medium text-gray-700">Suffix:</label>
                <x-select-option name="suffix" id="suffix" :default="old('suffix', $student->suffix ?? '')" 
                    :options="['Jr.' => 'Jr.', 'Sr.' => 'Sr.', 'II' => 'II', 'III' => 'III', 'IV' => 'IV']" class="mt-1 block w-full" />
                <x-input-error class="mt-2" :messages="$errors->get('suffix')" />
            </div>
        </div>

        <div class="flex justify-start">
            <div class="w-4/12 mr-6">
                <label for="student_number" class="block text-sm font-medium text-gray-700">Student Number:</label>
                <x-text-input id="student_number" name="student_number" type="text" class="w-3/12 mt-1 block w-full" :value="old('student_number', $student->student_number)"/>
            </div>
            <div class="w-4/12 mr-6">
                <label for="phone_number" class="block text-sm font-medium text-gray-700">Contact Number:</label>
                <x-text-input id="phone_number" name="phone_number" type="text" class="w-3/12 mt-1 block w-full" :value="old('phone_number', $student->phone_number)"/>
            </div>
            <div class="w-3/12 flex flex-col justify-around">
                <div class="flex items-center">
                    <label for="is_transferee" class="mr-2 block text-sm font-medium text-gray-700">Transferee:</label>
                    <x-checkbox id="is_transferee" name="is_transferee" :checked="old('is_transferee', $student->is_transferee) == '1'"/>
                </div>
                <div class="flex items-center">
                    <label for="is_irregular" class="mr-2 block text-sm font-medium text-gray-700">Irregular:</label>
                    <x-checkbox id="is_irregular" name="is_irregular" :checked="old('is_irregular', $student->is_irregular) == '1'"/>
                </div>
            </div>  
        </div>

        <div class="flex justify-start">
            <div class="w-6/12 mr-6">
                <label for="personal_email" class="block text-sm font-medium text-gray-700">Personal Email Address:</label>
                <x-text-input id="personal_email" name="personal_email" type="email" class="mt-1 block w-full" :value="old('personal_email', $student->personal_email)"/>
            </div>
            <div class="w-6/12 mr-6">
                <label for="school_email" class="block text-sm font-medium text-gray-700">School Email Address:</label>
                <x-text-input id="school_email" name="school_email" type="email" class="mt-1 block w-full" :value="old('school_email', $student->school_email)"/>
            </div>                      
        </div>
        <!-- Personal Information -->
        <div class="flex justify-start">
            <div class="w-4/12 mr-6">
                <label for="birthdate" class="block text-sm font-medium text-gray-700">Date of Birth:</label>
                <x-text-input id="birthdate" name="birthdate" type="date" class="mt-1 block w-full" :value="old('birthdate', $student->birthdate)"/>
            </div>
            <div class="w-4/12 mr-6">
                <label for="birthplace" class="block text-sm font-medium text-gray-700">Place of Birth:</label>
                <x-text-input id="birthplace" name="birthplace" type="text" class="mt-1 block w-full" :value="old('birthplace', $student->birthplace)"/>
            </div>
            <div class="w-1/12">
                <label for="sex" class="block text-sm font-medium text-gray-700">Sex:</label>
                <x-select-option name="sex" id="sex" :default="old('sex', $student->sex ?? '')" :options="['M' => 'M', 'F' => 'F']" class="mt-1 block w-full" />
            </div>
        </div>
        <div class="flex justify-start">
            <div class="w-4/12 mr-6">
                <label for="citizenship" class="block text-sm font-medium text-gray-700">Citizenship:</label>
                <x-text-input id="citizenship" name="citizenship" type="text" class="mt-1 block w-full" :value="old('citizenship', $student->citizenship)"/>
            </div>
            <div class="w-4/12 mr-6">
                <label for="civil_status" class="block text-sm font-medium text-gray-700">Civil Status:</label>
                <x-select-option name="civil_status" id="civil_status" :default="old('civil_status', $student->civil_status ?? '')" 
                    :options="['Single' => 'Single', 'Married' => 'Married', 'Widowed' => 'Widowed', 'Divorced' => 'Divorced', 'Separated' => 'Separated']" class="mt-1 block w-full" />
            </div>
            <div class="w-4/12 mr-6">
                <label for="religion" class="block text-sm font-medium text-gray-700">Religion:</label>
                <x-text-input id="religion" name="religion" type="text" class="mt-1 block w-full" :value="old('religion', $student->religion)"/>
            </div>
        </div>
        <div class="flex">
            <div class="w-1/12 mr-6">
                <label for="house_num" class="block text-sm font-medium text-gray-700">House No:</label>
                <x-text-input id="house_num" name="house_num" type="text" class="mt-1 block w-full" :value="old('house_num', $student->house_num)"/>
            </div>
            <div class="w-5/12 mr-6">
                <label for="street" class="block text-sm font-medium text-gray-700">Street Name:</label>
                <x-text-input id="street" name="street" type="text" class="mt-1 block w-full" :value="old('street', $student->street)"/>
            </div>
            <div class="w-5/12">
                <label for="brgy" class="block text-sm font-medium text-gray-700">Barangay:</label>
                <x-text-input id="brgy" name="brgy" type="text" class="mt-1 block w-full" :value="old('brgy', $student->brgy)"/>
            </div>
        </div>
        <div class="flex justify-between">
            <div class="w-5/12">
                <label for="city_municipality" class="block text-sm font-medium text-gray-700">City/Municipality:</label>
                <x-text-input id="city_municipality" name="city_municipality" type="text" class="mt-1 block w-full" :value="old('city_municipality', $student->city_municipality)"/>
            </div>
            <div class="w-5/12">
                <label for="province" class="block text-sm font-medium text-gray-700">Province:</label>
                <x-text-input id="province" name="province" type="text" class="mt-1 block w-full" :value="old('province', $student->province)"/>
            </div>
            <div class="w-1/12">
                <label for="zipcode" class="block text-sm font-medium text-gray-700">Zipcode:</label>
                <x-text-input id="zipcode" name="zipcode" type="text" class="mt-1 block w-full" :value="old('zipcode', $student->zipcode)"/>
            </div>
        </div>
        <div class="flex justify-start">
            <div class="w-6/12 mr-9">
                <label for="elementary" class="block text-sm font-medium text-gray-700">Primary:</label>
                <x-text-input id="elementary" name="elementary" type="text" class="mt-1 block w-full" :value="old('elementary', $student->elementary)"/>
            </div>
            <div class="w-2/12">
                <label for="elem_yr_grad" class="block text-sm font-medium text-gray-700">Year Graduated:</label>
                <x-select-option name="elem_yr_grad" id="elem_yr_grad" :default="old('elem_yr_grad', $student->elem_yr_grad ?? '')"
                                :options="$years" class="mt-1 block w-full" />
            </div>
        </div>
        <div class="flex justify-start">
            <div class="w-6/12 mr-9">
                <label for="highschool" class="block text-sm font-medium text-gray-700">Secondary:</label>
                <x-text-input id="highschool" name="highschool" type="text" class="mt-1 block w-full" :value="old('highschool', $student->highschool)"/>
            </div>
            <div class="w-2/12">
                <label for="hs_yr_grad" class="block text-sm font-medium text-gray-700">Year Graduated:</label>
                <x-select-option name="hs_yr_grad" id="hs_yr_grad" :default="old('hs_yr_grad', $student->hs_yr_grad ?? '')"
                                :options="$years" class="mt-1 block w-full" />
            </div>
        </div>
        <div class="flex justify-start">
            <div class="w-6/12 mr-9">
                <label for="college" class="block text-sm font-medium text-gray-700">College:</label>
                <x-text-input id="college" name="college" type="text" class="mt-1 block w-full" :value="old('college', $student->college)"/>
            </div>
            <div class="w-2/12">
                <label for="college_year_ended" class="block text-sm font-medium text-gray-700">Year Ended:</label>
                <x-select-option name="college_year_ended" id="college_year_ended" :default="old('college_year_ended', $student->college_year_ended ?? '')"
                                :options="$years" class="mt-1 block w-full" />
            </div>
        </div>
        <div class="flex justify-start">
            <div class="w-5/12 mr-6">
                <label for="guardian_name" class="block text-sm font-medium text-gray-700">Guardian Name:</label>
                <x-text-input id="guardian_name" name="guardian_name" type="text" class="mt-1 block w-full" :value="old('guardian_name', $student->guardian_name)"/>
            </div>
            <div class="w-5/12 mr-6">
                <label for="guardian_contact" class="block text-sm font-medium text-gray-700">Contact No.:</label>
                <x-text-input id="guardian_contact" name="guardian_contact" type="text" class="mt-1 block w-full" :value="old('guardian_contact', $student->guardian_contact)"/>
            </div>
        </div>
        <!-- Save button -->
        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
        </div>
    </form>
</section>
