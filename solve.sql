USE practice;
DROP PROCEDURE IF EXISTS dijkstra;
DROP TABLE IF EXISTS CulcGraph;
DELIMITER //
CREATE PROCEDURE dijkstra(IN startV INT, IN goalV INT, OUT result INT)
BEGIN
  -- nowV :  探索中の頂点
  DECLARE nowV INT;
  -- nowTotal :  前までの探索でnowVにたどり着くまでにかかったコスト合計
  DECLARE nowTotal INT;

  DECLARE E_MAX INT;
  DECLARE V_INF INT;
  DECLARE E_INF INT;
  DECLARE INF INT;

  SELECT MAX(cost) INTO E_MAX FROM edges;

--  同じ頂点を2度訪れることはない
  SELECT COUNT(id)*E_MAX INTO V_INF FROM vertices;
--  同じ辺を2度使うことはない
  SELECT COUNT(id)*E_MAX INTO E_INF FROM edges;
--  INF はグラフのスタート地点からゴール地点まで一筆で行った時、絶対に超えない値とする
  SET INF = CASE WHEN V_INF > E_INF THEN V_INF + 1 ELSE E_INF + 1 END;

  UPDATE vertices SET cost = 0 WHERE id = startV;
  DROP TABLE IF EXISTS CulcGraph;
  CREATE TABLE CulcGraph
  (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    tmpTotal INT UNSIGNED NOT NULL,
    tmpV INT UNSIGNED NOT NULL,
    INDEX tmpVT_idx(tmpV, tmpTotal),
    INDEX tmpTV_idx(tmpTotal, tmpV),
    PRIMARY KEY(id)
  );

  INSERT INTO CulcGraph (tmpTotal, tmpV) VALUES (0, startV);

  label1: LOOP
    IF (SELECT COUNT(*) FROM CulcGraph) = 0 THEN
      LEAVE label1;
    END IF;

    SELECT tmpTotal, tmpV 
    INTO nowTotal, nowV 
    FROM CulcGraph 
    ORDER BY tmpTotal ASC
    LIMIT 1;

    DELETE FROM CulcGraph 
    WHERE tmpTotal = nowTotal
      AND tmpV = nowV;

    IF (SELECT cost FROM vertices WHERE id = nowV) < nowTotal THEN
      ITERATE label1;
    END IF;

    -- index をつけるクエリ
    INSERT INTO CulcGraph(tmpTotal, tmpV) 
    (SELECT nowTotal + tmpEdges.cost, tmpEdges.dest
     FROM (
           SELECT MIN(edges.cost) AS cost, edges.dest
           FROM edges
           WHERE edges.src = nowV
           GROUP BY edges.dest
          ) tmpEdges
          INNER JOIN
            vertices
          ON tmpEdges.dest = vertices.id
     WHERE COALESCE(vertices.cost, INF) > nowTotal + tmpEdges.cost
    );

    UPDATE CulcGraph cg 
         INNER JOIN
           vertices Vs 
         ON cg.tmpV = Vs.id
    SET Vs.cost = CASE WHEN COALESCE(Vs.cost, INF) > tmpTotal THEN tmpTotal 
                       ELSE Vs.cost 
                  END;

  END LOOP label1;
  SET result = (SELECT cost FROM vertices WHERE id = goalV);
  DROP TABLE CulcGraph;
END
//
DELIMITER ;
