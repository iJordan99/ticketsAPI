<?php

namespace App\Observers;

use App\Models\AuditLog;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Illuminate\Support\Facades\Auth;

class ModelObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the "created" event for any model.
     */
    public function created($model): void
    {
        $this->logAction($model, 'created', $model->getAttributes());
    }

    protected function logAction($model, string $action, array $changes): void
    {
        AuditLog::create([
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'action' => $action,
            'changes' => json_encode($changes),
            'performed_by' => Auth::id(),
        ]);
    }

    /**
     * Handle the "updated" event for any model.
     */
    public function updated($model): void
    {
        $this->logAction($model, 'updated', $model->getChanges());
    }

    /**
     * Handle the "deleted" event for any model.
     */
    public function deleted($model): void
    {
        $this->logAction($model, 'deleted', $model->getOriginal());
    }
}
