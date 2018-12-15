<?php

namespace AppBundle\Util;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;

class DatatableUtil
{
    const SEARCH_EQUAL = '=';
    const SEARCH_LIKE = 'LIKE';

    /**
     * @param $requestData
     * @param $columns
     *
     * @return array|null
     */
    public static function getOrder($requestData, $columns)
    {
        $orderColIndex = isset($requestData['order'][0]['column']) ? $requestData['order'][0]['column'] : null;
        $orderCol = null;
        $orderDir = null;

        if (null !== $orderColIndex && !empty($columns[$orderColIndex])) {
            return [
                'col' => $columns[$orderColIndex],
                'dir' => $requestData['order'][0]['dir'],
            ];
        }

        return null;
    }

    /**
     * Create Expressions to use on querybuilder having.
     *
     * @param $requestData
     * @param $columns
     *
     * @return array|null
     */
    public static function getSearchs($requestData, $columns)
    {
        if (empty($requestData['columns'])) {
            return null;
        }

        $expr = new Expr();
        $searchs = [];
        foreach ($requestData['columns'] as $requestColumn) {
            $index = $requestColumn['data'];
            $value = $requestColumn['search']['value'];
            if (!empty($columns[$index]) && !empty($value)) {
                $columnName = $columns[$index]['name'];
                $searchType = $columns[$index]['searchType'];
                $columnKey = "{$columnName}Value";
                if (self::SEARCH_EQUAL === $searchType) {
                    $searchs[] = [
                        'expr' => $expr->eq($columnName, ":{$columnKey}"),
                        'param' => [
                            'key' => $columnKey,
                            'value' => $value,
                        ],
                    ];
                } elseif (self::SEARCH_LIKE === $searchType) {
                    $searchs[] = [
                        'expr' => new Expr\Comparison("Like($columnName, :{$columnKey})", '=', 1),
                        'param' => [
                            'key' => $columnKey,
                            'value' => "%{$value}%",
                        ],
                    ];
                }
            }
        }

        return count($searchs) > 0 ? $searchs : null;
    }

    /**
     * @param $requestData
     */
    public static function getLimit($requestData)
    {
        return isset($requestData['length']) ? $requestData['length'] : null;
    }

    /**
     * @param $requestData
     */
    public static function getOffset($requestData)
    {
        return isset($requestData['start']) ? $requestData['start'] : null;
    }

    /**
     * Build and execute all queries needed to populate a datatable.
     *
     * @param EntityManager $manager
     * @param QueryBuilder  $qb
     * @param $countAlias
     * @param $searchs
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public static function getQbData(EntityManager $manager, QueryBuilder $qb, $countAlias, $searchs)
    {
        $totalQb = clone $qb;
        $totalQb
            ->select("count(DISTINCT {$countAlias})")
            ->resetDQLParts(['having', 'orderBy', 'groupBy'])
            ->setMaxResults(null)
            ->setFirstResult(null)
        ;

        if (null !== $searchs) {
            $params = $totalQb->getParameters();
            foreach ($searchs as $search) {
                foreach ($params as $index => $param) {
                    if ($search['param']['key'] === $param->getName()) {
                        $params->removeElement($param);
                        break;
                    }
                }
            }
        }

        $data = $qb->getQuery()->getResult();
        $total = $totalQb->getQuery()->getSingleScalarResult();

        $totalFiltered = $total;
        if (null !== $searchs) {
            $paramsArray = [];
            foreach ($qb->getParameters() as $param) {
                $paramsArray[$param->getName()] = $param->getValue();
            }
            $pattern = '/'.implode(array_keys($paramsArray), '|').'/';
            if (count($paramsArray) > 0 && preg_match_all($pattern, $qb->getDQL(), $matches)) {
                $filteredParams = [];
                // Build the parameters array to une in the native query
                foreach ($matches[0] as $match) {
                    $filteredParams[] = $paramsArray[$match];
                }
                if (count($filteredParams) > 0) {
                    $filteredQb = clone $qb;
                    $filteredQb->setFirstResult(null)->setMaxResults(null);
                    $stmt = $manager->getConnection()->prepare("
                        SELECT count(*)
                        FROM ({$filteredQb->getQuery()->getSQL()}) subQuery
                    ");
                    $stmt->execute($filteredParams);
                    $totalFiltered = $stmt->fetchColumn();
                }
            }
        }

        return [
            'data' => $data,
            'recordsTotal' => $total,
            'recordsFiltered' => $totalFiltered,
        ];

        return [
            'data' => $data,
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
        ];
    }
}
