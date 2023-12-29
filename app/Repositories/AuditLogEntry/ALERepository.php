<?php

/*
 * ALERepository.php
 * Copyright (c) 2022 james@firefly-iii.org
 *
 * This file is part of Firefly III (https://github.com/firefly-iii).
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace FireflyIII\Repositories\AuditLogEntry;

use FireflyIII\Models\AuditLogEntry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class ALERepository
 */
class ALERepository implements ALERepositoryInterface
{
    public function getForObject(Model $model): Collection
    {
        // all Models have an ID.
        return AuditLogEntry::where('auditable_id', $model->id)->where('auditable_type', get_class($model))->get(); // @phpstan-ignore-line
    }

    public function store(array $data): AuditLogEntry
    {
        $auditLogEntry = new AuditLogEntry();

        $auditLogEntry->auditable()->associate($data['auditable']);
        $auditLogEntry->changer()->associate($data['changer']);
        $auditLogEntry->action = $data['action'];
        $auditLogEntry->before = $data['before'];
        $auditLogEntry->after  = $data['after'];
        $auditLogEntry->save();

        return $auditLogEntry;
    }
}
