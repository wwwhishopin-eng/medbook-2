<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditModelObserver
{
    public function created(Model $model): void
    {
        AuditLog::log(AuditLog::ACTION_CREATE, $model, null, $model->getAttributes());
    }

    public function updated(Model $model): void
    {
        AuditLog::log(AuditLog::ACTION_UPDATE, $model, $model->getOriginal(), $model->getChanges());
    }

    public function deleted(Model $model): void
    {
        AuditLog::log(AuditLog::ACTION_DELETE, $model, $model->getOriginal());
    }

    public function restored(Model $model): void
    {
        AuditLog::log(AuditLog::ACTION_RESTORE, $model);
    }
}
