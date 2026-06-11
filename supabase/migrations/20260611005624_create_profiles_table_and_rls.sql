-- Create profiles table for app-specific user data
CREATE TABLE profiles (
    id UUID PRIMARY KEY REFERENCES auth.users(id) ON DELETE CASCADE,
    name VARCHAR(255),
    app_role VARCHAR(20) DEFAULT 'doctor',
    phone VARCHAR(20),
    created_at TIMESTAMPTZ DEFAULT now(),
    updated_at TIMESTAMPTZ DEFAULT now()
);

CREATE INDEX idx_profiles_role ON profiles(app_role);

-- Insert profile for existing users
INSERT INTO profiles (id, name, app_role)
SELECT id, email, 'doctor'
FROM auth.users
WHERE id NOT IN (SELECT id FROM profiles);

-- Create RLS policies
ALTER TABLE profiles ENABLE ROW LEVEL SECURITY;

CREATE POLICY "profiles_select_own" ON profiles FOR SELECT
    USING (auth.uid() = id);

CREATE POLICY "profiles_update_own" ON profiles FOR UPDATE
    USING (auth.uid() = id);

-- RLS for patients table
ALTER TABLE patients ENABLE ROW LEVEL SECURITY;

CREATE POLICY "patients_select_all" ON patients FOR SELECT
    TO authenticated USING (true);

CREATE POLICY "patients_insert_all" ON patients FOR INSERT
    TO authenticated WITH CHECK (true);

CREATE POLICY "patients_update_all" ON patients FOR UPDATE
    TO authenticated USING (true);

CREATE POLICY "patients_delete_all" ON patients FOR DELETE
    TO authenticated USING (true);

-- RLS for appointments
ALTER TABLE appointments ENABLE ROW LEVEL SECURITY;

CREATE POLICY "appointments_select_all" ON appointments FOR SELECT
    TO authenticated USING (true);

CREATE POLICY "appointments_insert_all" ON appointments FOR INSERT
    TO authenticated WITH CHECK (true);

CREATE POLICY "appointments_update_all" ON appointments FOR UPDATE
    TO authenticated USING (true);

CREATE POLICY "appointments_delete_all" ON appointments FOR DELETE
    TO authenticated USING (true);

-- RLS for medical_histories
ALTER TABLE medical_histories ENABLE ROW LEVEL SECURITY;

CREATE POLICY "medical_histories_select_all" ON medical_histories FOR SELECT
    TO authenticated USING (true);

CREATE POLICY "medical_histories_insert_all" ON medical_histories FOR INSERT
    TO authenticated WITH CHECK (true);

CREATE POLICY "medical_histories_update_all" ON medical_histories FOR UPDATE
    TO authenticated USING (true);

CREATE POLICY "medical_histories_delete_all" ON medical_histories FOR DELETE
    TO authenticated USING (true);

-- RLS for waiting_list
ALTER TABLE waiting_list ENABLE ROW LEVEL SECURITY;

CREATE POLICY "waiting_list_select_all" ON waiting_list FOR SELECT
    TO authenticated USING (true);

CREATE POLICY "waiting_list_insert_all" ON waiting_list FOR INSERT
    TO authenticated WITH CHECK (true);

CREATE POLICY "waiting_list_update_all" ON waiting_list FOR UPDATE
    TO authenticated USING (true);

CREATE POLICY "waiting_list_delete_all" ON waiting_list FOR DELETE
    TO authenticated USING (true);

-- RLS for sms_confirmations
ALTER TABLE sms_confirmations ENABLE ROW LEVEL SECURITY;

CREATE POLICY "sms_confirmations_select_all" ON sms_confirmations FOR SELECT
    TO authenticated USING (true);

CREATE POLICY "sms_confirmations_insert_all" ON sms_confirmations FOR INSERT
    TO authenticated WITH CHECK (true);

-- RLS for prescriptions
ALTER TABLE prescriptions ENABLE ROW LEVEL SECURITY;

CREATE POLICY "prescriptions_select_all" ON prescriptions FOR SELECT
    TO authenticated USING (true);

CREATE POLICY "prescriptions_insert_all" ON prescriptions FOR INSERT
    TO authenticated WITH CHECK (true);

CREATE POLICY "prescriptions_update_all" ON prescriptions FOR UPDATE
    TO authenticated USING (true);

CREATE POLICY "prescriptions_delete_all" ON prescriptions FOR DELETE
    TO authenticated USING (true);