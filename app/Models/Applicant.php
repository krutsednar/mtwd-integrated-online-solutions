<?php

namespace App\Models;

use Carbon\Carbon;
use \DateTimeInterface;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Applicant extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable();
    }

    protected $connection = 'mocadb';

    public const SEX_SELECT = [
        'Male'   => 'Male',
        'Female' => 'Female',
    ];

    public const CIVIL_STATUS_SELECT = [
        'Single'    => 'Single',
        'Married'   => 'Married',
        'Separated' => 'Separated',
        'Widowed'   => 'Widowed',
    ];

    public const TRAINING_SELECT = [
        'No Training'     => 'No Training',
        '4 hours'         => '4 hours',
        '8 hours'         => '8 hours',
        '16 hours'        => '16 hours',
        '24 hours'        => '24 hours',
        '40 hours and up' => '40 hours and up',
    ];

    public const EXPERIENCE_SELECT = [
        'No Work Experience' => 'No Work  Experience',
        '1 year'             => '1 year',
        '2 years'            => '2 years',
        '3 years'            => '3 years',
        '4 years'            => '4 years',
        '5 years and up'     => '5 years and up',
    ];

    public const GRADUATE_STUDIES_SELECT = [
        'None'                       => 'None',
        'Master in Engineering'                       => 'Master in Engineering',
        'Master in Business Administration'           => 'Master in Business Administration',
        'Master in Public Administration'             => 'Master in Public Administration',
        'Master in Public Health'                     => 'Master in Public Health',
        'Master in Information Technology'            => 'Master in Information Technology',
        'Master of Industrial Technology'             => 'Master of Industrial Technology',
        'Master of Psychology'                        => 'Master of Psychology',
        'Master of Science in Information Technology' => 'Master of Science in Information Technology',
        'Master of Arts in Education'                 => 'Master of Arts in Education',
        'Master of Arts in Guidance and Counseling'   => 'Master of Arts in Guidance and Counseling',
        'Master of Arts in Psychology'                => 'Master of Arts in Psychology',
        'Bachelor of Laws and Letter'                 => 'Bachelor of Laws and Letter',
        'Doctor of Philosophy in Education'           => 'Doctor of Philosophy in Education',
        'Doctor in Public Administration'             => 'Doctor in Public Administration',
        'Doctor in Business Management'               => 'Doctor in Business Management',
        'Others'                                      => 'Others',
    ];

    public const ELIGIBILITY_SELECT = [
        'None'                       => 'None',
        'CS Professional'                                                     => 'CS Professional',
        'Honor Graduate Eligibility (PD 907)'                                 => 'Honor Graduate Eligibility (PD 907)',
        'Bar'                                                                 => 'Bar',
        'RA1080- (Architect)'                                                 => 'RA1080- (Architect)',
        'RA1080- (Certified Public Accountant)'                               => 'RA1080- (Certified Public Accountant)',
        'RA1080- (Chemical Engineer)'                                         => 'RA1080- (Chemical Engineer)',
        'RA1080- (Chemical Technician)'                                       => 'RA1080- (Chemical Technician)',
        'RA1080- (Chemist)'                                                   => 'RA1080- (Chemist)',
        'RA1080- (Civil Engineer)'                                            => 'RA1080- (Civil Engineer)',
        'RA1080- (Criminologist)'                                             => 'RA1080- (Criminologist)',
        'RA1080- (Electronics Engineer)'                                      => 'RA1080- (Electronics Engineer)',
        'RA1080- (Electronics Technician)'                                    => 'RA1080- (Electronics Technician)',
        'RA1080- (Forester)'                                                  => 'RA1080- (Forester)',
        'RA1080- (Geodetic Engineer)'                                         => 'RA1080- (Geodetic Engineer)',
        'RA1080- (Guidance Counselor)'                                        => 'RA1080- (Guidance Counselor)',
        'RA1080- (Master Plumber)'                                            => 'RA1080- (Master Plumber)',
        'RA1080- (Mechanical Engineer)'                                       => 'RA1080- (Mechanical Engineer)',
        'RA1080- (Medical Technologist)'                                      => 'RA1080- (Medical Technologist)',
        'RA1080- (Metallurgical Engineer)'                                    => 'RA1080- (Metallurgical Engineer)',
        'RA1080- (Nurse)'                                                     => 'RA1080- (Nurse)',
        'RA1080- (Professional Electrical Engineer)'                          => 'RA1080- (Professional Electrical Engineer)',
        'RA1080- (Professional Teachers)'                                     => 'RA1080- (Professional Teachers)',
        'RA1080- (Psychologist)'                                              => 'RA1080- (Psychologist)',
        'RA1080- (Psychometrician)'                                           => 'RA1080- (Psychometrician)',
        'RA1080- (Real Estate Appraiser)'                                     => 'RA1080- (Real Estate Appraiser)',
        'RA1080- (Real Estate Broker)'                                        => 'RA1080- (Real Estate Broker)',
        'RA1080- (Registered Electrical Engineer)'                            => 'RA1080- (Registered Electrical Engineer)',
        'RA1080- (Registered Master Electrician)'                             => 'RA1080- (Registered Master Electrician)',
        'RA1080- (Sanitary Engineer)'                                         => 'RA1080- (Sanitary Engineer)',
        'RA1080- (Social Worker)'                                             => 'RA1080- (Social Worker)',
        'Barangay Official Eligibility (RA 7160)'                             => 'Barangay Official Eligibility (RA 7160)',
        'Electronic Data Processing Specialist Eligibility (CSC Res. 90-083)' => 'Electronic Data Processing Specialist Eligibility (CSC Res. 90-083)',
        'Skills Eligibility - Category II (CSC MC 11, s. 1996, as Amended)'   => 'Skills Eligibility - Category II (CSC MC 11, s. 1996, as Amended)',
        'TESDA National Certificate'                                          => 'TESDA National Certificate',
        'No Eligibility'                                                      => 'No Eligibility',
        'Others'                                                              => 'Others',
    ];

    public const EDUCATION_SELECT = [
        'None'                       => 'None',
        'Elementary Graduate'                                            => 'Elementary Graduate',
        'High School Graduate'                                           => 'High School Graduate',
        'Bachelor of Science in Agricultural and Bio System Engineering' => 'Bachelor of Science in Agricultural and Bio System Engineering',
        'Bachelor of Science in Chemical Engineering'                    => 'Bachelor of Science in Chemical Engineering',
        'Bachelor of Science in Civil Engineering'                       => 'Bachelor of Science in Civil Engineering',
        'Bachelor of Science in Computer Engineering'                    => 'Bachelor of Science in Computer Engineering',
        'Bachelor of Science in Electrical Engineering'                  => 'Bachelor of Science in Electrical Engineering',
        'Bachelor of Science in Electronics Engineering'                 => 'Bachelor of Science in Electronics Engineering',
        'Bachelor of Science in Environmental and Sanitary Engineering'  => 'Bachelor of Science in Environmental and Sanitary Engineering',
        'Bachelor of Science in Geodetic Engineering'                    => 'Bachelor of Science in Geodetic Engineering',
        'Bachelor of Science in Mechanical Engineering'                  => 'Bachelor of Science in Mechanical Engineering',
        'Bachelor of Arts in Communication'                              => 'Bachelor of Arts in Communication',
        'Bachelor of Arts in Economics'                                  => 'Bachelor of Arts in Economics',
        'Bachelor of Arts in Legal Management'                           => 'Bachelor of Arts in Legal Management',
        'Bachelor of Arts in Political Science'                          => 'Bachelor of Arts in Political Science',
        'Bachelor of Elementary Education'                               => 'Bachelor of Elementary Education',
        'Bachelor of Public Administration'                              => 'Bachelor of Public Administration',
        'Bachelor of Science in Accountancy'                             => 'Bachelor of Science in Accountancy',
        'Bachelor of Science in Architecture'                            => 'Bachelor of Science in Architecture',
        'Bachelor of Science in Biology'                                 => 'Bachelor of Science in Biology',
        'Bachelor of Science in Business Administration'                 => 'Bachelor of Science in Business Administration',
        'Bachelor of Science in Chemistry'                               => 'Bachelor of Science in Chemistry',
        'Bachelor of Science in Computer Science'                        => 'Bachelor of Science in Computer Science',
        'Bachelor of Science in Criminology'                             => 'Bachelor of Science in Criminology',
        'Bachelor of Science in Development Communication'               => 'Bachelor of Science in Development Communication',
        'Bachelor of Science in Industrial Technology'                   => 'Bachelor of Science in Industrial Technology',
        'Bachelor of Science in Information System/Technology'           => 'Bachelor of Science in Information System/Technology',
        'Bachelor of Science in Legal Management'                        => 'Bachelor of Science in Legal Management',
        'Bachelor of Science in Management Accounting'                   => 'Bachelor of Science in Management Accounting',
        'Bachelor of Science in Mathematics'                             => 'Bachelor of Science in Mathematics',
        'Bachelor of Science in Medical Technology'                      => 'Bachelor of Science in Medical Technology',
        'Bachelor of Science in Nursing'                                 => 'Bachelor of Science in Nursing',
        'Bachelor of Science in Psychology'                              => 'Bachelor of Science in Psychology',
        'Bachelor of Science in Public Administration'                   => 'Bachelor of Science in Public Administration',
        'Bachelor of Science in Public Health'                           => 'Bachelor of Science in Public Health',
        'Bachelor of Science in Social Work'                             => 'Bachelor of Science in Social Work',
        'Bachelor of Science in Tourism Management'                      => 'Bachelor of Science in Tourism Management',
        'Bachelor of Secondary Education'                                => 'Bachelor of Secondary Education',
        'Vocational or Trade Course'                                     => 'Vocational or Trade Course',
        'Others'                                                         => 'Others',
    ];

    public $table = 'applicants';

    public $orderable = [
        'id',
        'code',
        'item_number',
        'position_title',
        'first_name',
        'middle_name',
        'last_name',
        'street',
        'city',
        'province',
        'zip_code',
        'email',
        'mobile_number',
        'birthday',
        'sex',
        'civil_status',
        'eligibility',
        'eligibility_input',
        'education',
        'education_input',
        'graduate_studies',
        'graduate_studies_input',
        'experience',
        'training',
        'training_input',
        'status',
        'experience_input',
    ];

    public $filterable = [
        'id',
        'code',
        'item_number',
        'position_title',
        'first_name',
        'middle_name',
        'last_name',
        'street',
        'city',
        'province',
        'zip_code',
        'email',
        'mobile_number',
        'birthday',
        'sex',
        'civil_status',
        'eligibility',
        'eligibility_input',
        'education',
        'education_input',
        'graduate_studies',
        'graduate_studies_input',
        'experience',
        'training',
        'training_input',
        'status',
        'experience_input',
    ];

    protected $dates = [
        'birthday',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'application_letter',
        'personal_data_sheet',
        'eligibility_attachment',
        'education_attachment',
        'graduate_attachment',
        'experience_attachment',
        'training_attachment',
    ];

    protected $fillable = [
        'code',
        'item_number',
        'position_title',
        'first_name',
        'middle_name',
        'last_name',
        'street',
        'city',
        'province',
        'zip_code',
        'email',
        'mobile_number',
        'birthday',
        'sex',
        'civil_status',
        'eligibility',
        'eligibility_input',
        'education',
        'education_input',
        'graduate_studies',
        'graduate_studies_input',
        'experience',
        'training',
        'training_input',
        'status',
        'experience_input',
    ];

    public function getApplicationLetterAttribute()
    {
        return $this->getMedia('applicant_application_letter')->map(function ($item) {
            $media = $item->toArray();
            $media['url'] = $item->getUrl();

            return $media;
        });
    }

    public function getPersonalDataSheetAttribute()
    {
        return $this->getMedia('applicant_personal_data_sheet')->map(function ($item) {
            $media = $item->toArray();
            $media['url'] = $item->getUrl();

            return $media;
        });
    }

    public function getBirthdayAttribute($value)
    {
        return $value ? Carbon::parse($value)->format(config('project.date_format')) : null;
    }

    public function setBirthdayAttribute($value)
    {
        $this->attributes['birthday'] = $value ? Carbon::createFromFormat(config('project.date_format'), $value)->format('Y-m-d') : null;
    }

    public function getSexLabelAttribute($value)
    {
        return static::SEX_SELECT[$this->sex] ?? null;
    }

    public function getCivilStatusLabelAttribute($value)
    {
        return static::CIVIL_STATUS_SELECT[$this->civil_status] ?? null;
    }

    public function getEligibilityLabelAttribute($value)
    {
        return static::ELIGIBILITY_SELECT[$this->eligibility] ?? null;
    }

    public function getEligibilityAttachmentAttribute()
    {
        return $this->getMedia('applicant_eligibility_attachment')->map(function ($item) {
            $media = $item->toArray();
            $media['url'] = $item->getUrl();

            return $media;
        });
    }

    public function getEducationLabelAttribute($value)
    {
        return static::EDUCATION_SELECT[$this->education] ?? null;
    }

    public function getEducationAttachmentAttribute()
    {
        return $this->getMedia('applicant_education_attachment')->map(function ($item) {
            $media = $item->toArray();
            $media['url'] = $item->getUrl();

            return $media;
        });
    }

    public function getGraduateStudiesLabelAttribute($value)
    {
        return static::GRADUATE_STUDIES_SELECT[$this->graduate_studies] ?? null;
    }

    public function getGraduateAttachmentAttribute()
    {
        return $this->getMedia('applicant_graduate_attachment')->map(function ($item) {
            $media = $item->toArray();
            $media['url'] = $item->getUrl();

            return $media;
        });
    }

    public function getExperienceLabelAttribute($value)
    {
        return static::EXPERIENCE_SELECT[$this->experience] ?? null;
    }

    public function getExperienceAttachmentAttribute()
    {
        return $this->getMedia('applicant_experience_attachment')->map(function ($item) {
            $media = $item->toArray();
            $media['url'] = $item->getUrl();

            return $media;
        });
    }

    public function getTrainingLabelAttribute($value)
    {
        return static::TRAINING_SELECT[$this->training] ?? null;
    }

    public function getTrainingAttachmentAttribute()
    {
        return $this->getMedia('applicant_training_attachment')->map(function ($item) {
            $media = $item->toArray();
            $media['url'] = $item->getUrl();

            return $media;
        });
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }
}
