-- ---------------------------------------------------------------------------
-- CREATE STRUCTURE
-- ---------------------------------------------------------------------------

INSERT INTO
    public.pia_structure (
        id,
        name,
        created_at,
        updated_at,
        type_id,
        portfolio_id
    )
VALUES
    (
        nextval('pia_structure_id_seq'),
        'StructureCI',
        '2018-08-07 12:09:15',
        '2018-08-07 12:09:15',
        NULL,
        NULL
    );
    
-- ---------------------------------------------------------------------------
-- CREATE PROCESSING
-- ---------------------------------------------------------------------------

INSERT INTO
    pia_processing (
        id,
        folder_id,
        name,
        author,
        description,
        processors,
        controllers,
        non_eu_transfer,
        life_cycle,
        storage,
        standards,
        status,
        created_at,
        updated_at
    )
VALUES
    (
        nextval('pia_processing_id_seq'),
        397,
        'Processing CI',
        'Author 1',
        NULL,
        NULL,
        'Controller 1, Controller 2, Controller 3',
        NULL,
        NULL,
        NULL,
        NULL,
        0,
        '2018-08-07 11:51:58',
        '2018-08-07 11:51:58'
    );
    
-- ---------------------------------------------------------------------------
-- CREATE PIA
-- ---------------------------------------------------------------------------

INSERT INTO
    pia (
        id,
        status,
        name,
        author_name,
        evaluator_name,
        validator_name,
        dpo_status,
        dpo_opinion,
        concerned_people_opinion,
        concerned_people_status,
        concerned_people_searched_opinion,
        concerned_people_searched_content,
        rejection_reason,
        applied_adjustements,
        dpos_names,
        people_names,
        is_example,
        created_at,
        updated_at,
        structure_id,
        template_id,
        type,
        processing_id
    )
VALUES
    (
        nextval('pia_id_seq'),
        0,
        'codecept-name',
        'codecept-author',
        'codecept-evaluator',
        'codecept-validator',
        0,
        '',
        '',
        0,
        false,
        NULL,
        '',
        '',
        '',
        '',
        false,
        '2018-08-07 11:51:58',
        '2018-08-07 11:51:58',
        NULL,
        NULL,
        'regular',
        37
    );