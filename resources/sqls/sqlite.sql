-- #! sqlite

-- #{ libasynqlTest

    -- #{ init
        -- #{ startlog
CREATE TABLE IF NOT EXISTS startlog (
  id INTEGER NOT NULL PRIMARY KEY,
  start_time INTEGER NOT NULL,
  example_message TEXT NOT NULL
);
        -- #}
    -- #}

    -- #{ add
        -- #{ startlog
        -- #:start_time int
        -- #:example_message string
INSERT INTO startlog (
  start_time,
  example_message
) VALUES (
  :start_time,
  :example_message
);
        -- #}
    -- #}

    -- #{ get
        -- #{ startlog
SELECT * FROM startlog;
        -- #}
    -- #}

-- #}