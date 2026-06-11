-- Create patients table
CREATE TABLE patients (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    first_name VARCHAR(60) NOT NULL,
    last_name VARCHAR(60) NOT NULL,
    national_id VARCHAR(10) UNIQUE,
    date_of_birth DATE,
    gender VARCHAR(10) DEFAULT 'male',
    phone VARCHAR(20),
    email VARCHAR(150),
    address TEXT,
    blood_type VARCHAR(5),
    conditions JSONB DEFAULT '[]'::jsonb,
    allergies JSONB DEFAULT '[]'::jsonb,
    emergency_contact_name VARCHAR(120),
    emergency_contact_phone VARCHAR(20),
    status VARCHAR(20) DEFAULT 'active',
    notes TEXT,
    avatar_color VARCHAR(10) DEFAULT '#2E5BFF',
    deleted_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

CREATE INDEX idx_patients_name ON patients(first_name, last_name);
CREATE INDEX idx_patients_phone ON patients(phone);
CREATE INDEX idx_patients_status ON patients(status);

-- Create appointments table
CREATE TABLE appointments (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    patient_id UUID NOT NULL REFERENCES patients(id) ON DELETE CASCADE,
    created_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    title VARCHAR(255) NOT NULL,
    start_at TIMESTAMPTZ NOT NULL,
    end_at TIMESTAMPTZ,
    status VARCHAR(20) DEFAULT 'reserved',
    type VARCHAR(20) DEFAULT 'checkup',
    notes TEXT,
    reminder_sent BOOLEAN DEFAULT false,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

CREATE INDEX idx_appointments_patient ON appointments(patient_id);
CREATE INDEX idx_appointments_start ON appointments(start_at);
CREATE INDEX idx_appointments_status ON appointments(status);

-- Create medical_histories table
CREATE TABLE medical_histories (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    patient_id UUID NOT NULL REFERENCES patients(id) ON DELETE CASCADE,
    created_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    visit_date DATE NOT NULL,
    visit_type VARCHAR(20) DEFAULT 'follow_up',
    chief_complaint VARCHAR(255),
    diagnosis TEXT,
    treatment TEXT,
    prescriptions TEXT,
    lab_results JSONB,
    vital_signs JSONB,
    doctor_notes TEXT,
    follow_up_date DATE,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

CREATE INDEX idx_medical_histories_patient ON medical_histories(patient_id, visit_date);

-- Create prescriptions table
CREATE TABLE prescriptions (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    patient_id UUID NOT NULL REFERENCES patients(id) ON DELETE CASCADE,
    medical_history_id UUID REFERENCES medical_histories(id) ON DELETE SET NULL,
    created_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    prescribed_at DATE NOT NULL,
    medications JSONB DEFAULT '[]'::jsonb,
    instructions TEXT,
    duration_days INTEGER,
    refills_allowed SMALLINT DEFAULT 0,
    notes TEXT,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

CREATE INDEX idx_prescriptions_patient ON prescriptions(patient_id, prescribed_at);

-- Create waiting_list table
CREATE TABLE waiting_list (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    patient_id UUID NOT NULL REFERENCES patients(id) ON DELETE CASCADE,
    preferred_date DATE,
    preferred_time_start TIME,
    preferred_time_end TIME,
    status VARCHAR(20) DEFAULT 'waiting',
    notes TEXT,
    created_by UUID REFERENCES auth.users(id) ON DELETE SET NULL,
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

CREATE INDEX idx_waiting_list_status ON waiting_list(status);
CREATE INDEX idx_waiting_list_patient ON waiting_list(patient_id);

-- Create sms_confirmations table
CREATE TABLE sms_confirmations (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    appointment_id UUID NOT NULL REFERENCES appointments(id) ON DELETE CASCADE,
    mobile VARCHAR(20) NOT NULL,
    message_type VARCHAR(20) DEFAULT 'confirmation_request',
    status VARCHAR(20) DEFAULT 'pending',
    sent_at TIMESTAMPTZ DEFAULT now(),
    responded_at TIMESTAMPTZ,
    response VARCHAR(10),
    created_at TIMESTAMPTZ DEFAULT now()
);

CREATE INDEX idx_sms_confirmations_appointment ON sms_confirmations(appointment_id);
CREATE INDEX idx_sms_confirmations_status ON sms_confirmations(status);

-- Update subscriptions table to have user reference be nullable
ALTER TABLE subscriptions ALTER COLUMN user_id DROP NOT NULL;