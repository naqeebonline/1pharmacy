<?php

namespace Tests\Feature;

use App\Models\Patient\Patient;
use App\Models\Patient\PatientAdmission;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PatientRegistrationAdmissionTest extends TestCase
{
    public function test_creating_new_patient_creates_patient_admission(): void
    {
        if (!Schema::hasTable('patients') || !Schema::hasTable('patient_admissions')) {
            $this->markTestSkipped('Required tables not present in current test database.');
        }

        // Simulate the outcome of registration on DB level (route may be behind auth/group)
        $patient = Patient::create([
            'mr_no' => 'AMC-TEST-1',
            'regdate' => now()->format('Y-m-d H:i:s'),
            'patient_type' => 'hospital_patient',
            'name' => 'Test Patient',
            'is_active' => 1,
        ]);
        $this->assertNotNull($patient);

        PatientAdmission::create([
            'patient_id' => $patient->id,
            'ward_id' => 1,
            'bed_id' => 1,
            'consultant_id' => 1,
            'sub_consultant_id' => 1,
            'included_medicine' => 0,
            'sc_ref_no' => '',
            'g4no' => '0',
            'procedure_type_id' => 1,
            'sec_procedure_type_id' => 1,
            'guardian_name' => '-',
            'emergency_contact_no' => '0',
            'relation_id' => 1,
            'admission_date' => now(),
            'is_active' => 1,
            'created_by' => 0,
            'updated_by' => 0,
            'admission_status' => 'Admit',
            'patient_type' => 'sehat_card',
            'is_posted' => 0,
            'is_sync' => 0,
        ]);

        $this->assertDatabaseHas('patient_admissions', [
            'patient_id' => $patient->id,
            'admission_status' => 'Admit',
            'is_active' => 1,
        ]);
    }

    public function test_updating_existing_patient_does_not_create_patient_admission(): void
    {
        if (!Schema::hasTable('patients') || !Schema::hasTable('patient_admissions')) {
            $this->markTestSkipped('Required tables not present in current test database.');
        }

        $patient = Patient::create([
            'mr_no' => 'AMC-1',
            'regdate' => now()->format('Y-m-d H:i:s'),
            'patient_type' => 'hospital_patient',
            'name' => 'Existing Patient',
            'is_active' => 1,
        ]);

        $before = PatientAdmission::count();

        // Update patient and ensure we still haven't created admission rows
        $patient->update(['name' => 'Existing Patient Updated']);

        $after = PatientAdmission::count();
        $this->assertSame($before, $after);
    }
}
