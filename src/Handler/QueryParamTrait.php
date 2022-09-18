<?php

namespace App\Handler;

use Psr\Http\Message\ServerRequestInterface;

trait QueryParamTrait
{
    /**
     * Takes in the $request, extracts the `malaflokkur` query-param
     * and splits it into array. Removes the empty string if found.
     * Returns all `types` as lower-cases.
     */
    public function extractType(ServerRequestInterface $request): array
    {
        $queryString = $request->getQueryParams()['malaflokkur'] ?? '';
        $explodedTypes = explode(',', $queryString);
        $trimmedType = array_map(fn ($param) => trim(strtolower($param)), $explodedTypes);
        $filteredTypes = array_filter($trimmedType, fn ($i) => (!empty($i)));
        return $filteredTypes;
    }

    /**
     * Takes in the $request, extracts the `tegund` query-param
     * validates that the value IS NOT `varamenn` and returns `true`
     * else returns `false`.
     */
    public function extractCongressmanIsPrimary(ServerRequestInterface $request): bool
    {
        $queryString = $request->getQueryParams()['tegund'] ?? '';
        return strtolower($queryString) === 'varamenn' ? false : true;
    }

    /**
     * Takes in the $request, extracts the `bendill` query-param
     * returns the value or `null` if param is not set or doesn't have a value.
     */
    public function extractPointer(ServerRequestInterface $request): ?string
    {
        $params = $request->getQueryParams();
        return isset($params['bendill']) ? $params['bendill'] : null;
    }
}
