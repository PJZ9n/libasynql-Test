<?php
    
    /**
     * Copyright (c) 2020 PJZ9n.
     *
     * This file is part of libasynql-Test.
     *
     * libasynql-Test is free software: you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation, either version 3 of the License, or
     * (at your option) any later version.
     *
     * libasynql-Test is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     * GNU General Public License for more details.
     *
     * You should have received a copy of the GNU General Public License
     * along with libasynql-Test.  If not, see <http://www.gnu.org/licenses/>.
     */
    
    declare(strict_types=1);
    
    namespace PJZ9n\libasynqlTest;
    
    use pocketmine\plugin\PluginBase;
    use poggit\libasynql\DataConnector;
    use poggit\libasynql\libasynql;
    
    class libasynqlTest extends PluginBase
    {
        
        /** @var DataConnector */
        private $db;
        
        public function onEnable(): void
        {
            $this->saveDefaultConfig();
            $this->reloadConfig();
            
            $this->db = libasynql::create($this, $this->getConfig()->get("database"), [
                "sqlite" => "sqls/sqlite.sql",
            ]);
            
            //init
            $this->getLogger()->info("初期化処理を開始します！");
            $this->db->executeGeneric("libasynqlTest.init.startlog", [], function () {
                $this->getLogger()->info("成功！");
            });
            
            //実際にログに入れてみる
            $this->getLogger()->info("ログに入れます！");
            $this->db->executeInsert("libasynqlTest.add.startlog", [
                "start_time" => time(),
                "example_message" => "Hello World libasynql!",
            ], function (int $insertId, int $affectedRows) {
                //$insertIdは、挿入した行のID(連番)みたい。
                //$affectedRowsは、おそらく作った(影響を受けた？)数
                //複数のクエリを同時に送ると変わるかも？
                $this->getLogger()->info("ログに {InsertId: {$insertId}, AffectedRows: {$affectedRows}} で入れました！");
                $this->getLogger()->info("END.");
            });
        }
        
        public function onDisable(): void
        {
            
            //取得
            //これが上手くいかない。非同期で処理をしているからだと思う
            //多分取得中にdbが閉まってるし取得出来た場合でもスレッドが死んでる
            //現状closeしてから普通に開いて、、、しか思いつかない
            $this->getLogger()->info("取得します！");
            $this->db->executeSelect("libasynqlTest.get.startlog", [], function (array $rows) {
                $this->getLogger()->info("取得しました！");
                print_r($rows);
                $this->getLogger()->info("END.");
            });
            
            //全部のクエリが処理されるまで待機(Thanks: @y_fy_)
            $this->db->waitAll();
            
            if (isset($this->db)) {
                $this->db->close();
            }
        }
        
    }