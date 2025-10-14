<?php

namespace App\Traits;

trait ServiceNowStateMapper
{
    /**
     * Map ServiceNow numeric state to a human-readable string.
     */
    private function mapServiceNowState($state): string
    {
        $stateMap = [
            1 => 'New',
            2 => 'In Progress',
            3 => 'On Hold',
            6 => 'Resolved',
            7 => 'Closed',
            8 => 'Canceled'
        ];

        // If the state is a number and we have a mapping, return the mapped string.
        if (is_numeric($state) && isset($stateMap[(int)$state])) {
            return $stateMap[(int)$state];
        }
        
        // If the state is a non-empty string, return it directly.
        if (is_string($state) && !empty($state)) {
            return $state;
        }

        // If the state is unmapped or invalid, return 'Unknown'.
        return 'Unknown';
    }
}