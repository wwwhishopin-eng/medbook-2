-- Subscription plans
CREATE TABLE subscription_plans (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    price_monthly INTEGER NOT NULL DEFAULT 0,
    price_yearly INTEGER NOT NULL DEFAULT 0,
    features JSONB DEFAULT '{}',
    max_patients INTEGER DEFAULT NULL,
    max_appointments_per_day INTEGER DEFAULT NULL,
    max_users INTEGER DEFAULT 1,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INTEGER DEFAULT 0,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW()
);

-- Insert default plans
INSERT INTO subscription_plans (name, slug, description, price_monthly, price_yearly, features, sort_order) VALUES
('پایه', 'basic', 'پلن مناسب برای مطب‌های کوچک', 150000, 1500000, '{"sms_limit": 100, "voice_calls": false, "custom_reports": false}', 1),
('حرفه‌ای', 'professional', 'پلن پیشنهادی برای مطب‌ها', 350000, 3500000, '{"sms_limit": 500, "voice_calls": true, "custom_reports": true}', 2),
(' سازمانی', 'enterprise', 'پلن کامل با قابلیت‌های پیشرفته', 750000, 7500000, '{"sms_limit": -1, "voice_calls": true, "custom_reports": true, "api_access": true}', 3);

-- Clinic/Practice subscriptions
CREATE TABLE subscriptions (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL REFERENCES auth.users(id) ON DELETE CASCADE,
    plan_id UUID NOT NULL REFERENCES subscription_plans(id),
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    license_key VARCHAR(64) UNIQUE NOT NULL,
    trial_ends_at DATE,
    starts_at DATE NOT NULL,
    expires_at DATE NOT NULL,
    cancelled_at DATE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT NOW(),
    CONSTRAINT valid_status CHECK (status IN ('pending', 'active', 'trial', 'expired', 'cancelled', 'suspended'))
);

CREATE INDEX idx_subscriptions_user ON subscriptions(user_id);
CREATE INDEX idx_subscriptions_license ON subscriptions(license_key);
CREATE INDEX idx_subscriptions_status ON subscriptions(status);
CREATE INDEX idx_subscriptions_expires ON subscriptions(expires_at);

-- Enable RLS
ALTER TABLE subscription_plans ENABLE ROW LEVEL SECURITY;
ALTER TABLE subscriptions ENABLE ROW LEVEL SECURITY;

-- RLS Policies for subscription_plans (read-only for all authenticated users)
CREATE POLICY "select_subscription_plans" ON subscription_plans FOR SELECT
    TO authenticated USING (true);

-- RLS Policies for subscriptions
CREATE POLICY "select_own_subscription" ON subscriptions FOR SELECT
    TO authenticated USING (auth.uid() = user_id);

CREATE POLICY "insert_own_subscription" ON subscriptions FOR INSERT
    TO authenticated WITH CHECK (auth.uid() = user_id);

CREATE POLICY "update_own_subscription" ON subscriptions FOR UPDATE
    TO authenticated USING (auth.uid() = user_id) WITH CHECK (auth.uid() = user_id);

-- Function to generate license key
CREATE OR REPLACE FUNCTION generate_license_key()
RETURNS VARCHAR AS $$
DECLARE
    chars TEXT := 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    result VARCHAR(64) := '';
    i INTEGER;
BEGIN
    FOR i IN 1..16 LOOP
        result := result || substr(chars, floor(random() * length(chars) + 1):: INTEGER, 1);
        IF i % 4 = 0 AND i < 16 THEN
            result := result || '-';
        END IF;
    END LOOP;
    RETURN result;
END;
$$ LANGUAGE plpgsql;

-- Trigger for updated_at
CREATE OR REPLACE FUNCTION update_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER subscription_plans_updated_at
    BEFORE UPDATE ON subscription_plans
    FOR EACH ROW EXECUTE FUNCTION update_updated_at();

CREATE TRIGGER subscriptions_updated_at
    BEFORE UPDATE ON subscriptions
    FOR EACH ROW EXECUTE FUNCTION update_updated_at();