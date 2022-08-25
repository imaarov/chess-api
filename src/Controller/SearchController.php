<?php

namespace ChessApi\Controller;

use ChessApi\Pdo;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SearchController extends AbstractController
{
    public function index(Request $request): Response
    {
        $params = json_decode($request->getContent(), true);

        $sql = 'SELECT * FROM players WHERE ';
        $values = [];
        foreach ($params as $key => $val) {
            $sql .= "$key LIKE :$key AND ";
            $values[] = [
                'param' => ":$key",
                'value' => '%'.$val.'%',
                'type' => \PDO::PARAM_STR,
            ];
        }
        str_ends_with($sql, 'WHERE ')
            ? $sql = substr($sql, 0, -6)
            : $sql = substr($sql, 0, -4);
        $sql .= 'ORDER BY RAND() LIMIT 25';

        $arr = Pdo::getInstance($this->getParameter('pdo'))
            ->query($sql, $values)
            ->fetchAll(\PDO::FETCH_ASSOC);

        if ($arr) {
            return $this->json($arr);
        }

        $response = new Response();
        $response->setStatusCode(Response::HTTP_NO_CONTENT);

        return $response;
    }
}