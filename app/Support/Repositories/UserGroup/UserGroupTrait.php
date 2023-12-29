<?php

/*
 * UserGroupTrait.php
 * Copyright (c) 2023 james@firefly-iii.org
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

namespace FireflyIII\Support\Repositories\UserGroup;

use FireflyIII\Exceptions\FireflyException;
use FireflyIII\Models\GroupMembership;
use FireflyIII\Models\UserGroup;
use FireflyIII\User;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Trait UserGroupTrait
 */
trait UserGroupTrait
{
    protected User      $user;
    protected UserGroup $userGroup;

    public function getUserGroup(): UserGroup
    {
        return $this->userGroup;
    }

    /**
     * TODO This method does not check if the user has access to this particular user group.
     */
    public function setUserGroup(UserGroup $userGroup): void
    {
        $this->userGroup = $userGroup;
    }

    /**
     * @throws FireflyException
     */
    public function setUser(null|Authenticatable|User $user): void
    {
        if ($user instanceof User) {
            $this->user = $user;
            if (null === $user->userGroup) {
                throw new FireflyException(sprintf('User #%d has no user group.', $user->id));
            }
            $this->userGroup = $user->userGroup;
        }
    }

    /**
     * @throws FireflyException
     */
    public function setUserGroupById(int $userGroupId): void
    {
        $memberships = GroupMembership::where('user_id', $this->user->id)
            ->where('user_group_id', $userGroupId)
            ->count()
        ;
        if (0 === $memberships) {
            throw new FireflyException(sprintf('User #%d has no access to administration #%d', $this->user->id, $userGroupId));
        }

        /** @var null|UserGroup $userGroup */
        $userGroup = UserGroup::find($userGroupId);
        if (null === $userGroup) {
            throw new FireflyException(sprintf('Cannot find administration for user #%d', $this->user->id));
        }
        $this->userGroup = $userGroup;
    }
}
