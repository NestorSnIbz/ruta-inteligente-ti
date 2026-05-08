CREATE TABLE persona (
    id_persona SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL
);

CREATE TABLE proyecto (
    id_proyecto SERIAL PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    creador_id INT NOT NULL,

    CONSTRAINT fk_proyecto_creador
        FOREIGN KEY (creador_id)
        REFERENCES persona(id_persona)
);

CREATE TABLE proyecto_miembro (
    id SERIAL PRIMARY KEY,
    id_proyecto INT NOT NULL,
    id_persona INT NOT NULL,
    rol VARCHAR(50),

    CONSTRAINT fk_pm_proyecto
        FOREIGN KEY (id_proyecto)
        REFERENCES proyecto(id_proyecto)
        ON DELETE CASCADE,

    CONSTRAINT fk_pm_persona
        FOREIGN KEY (id_persona)
        REFERENCES persona(id_persona)
        ON DELETE CASCADE
);

CREATE TABLE vision (
    id_vision SERIAL PRIMARY KEY,
    id_proyecto INT UNIQUE NOT NULL,
    descripcion TEXT NOT NULL,

    CONSTRAINT fk_vision_proyecto
        FOREIGN KEY (id_proyecto)
        REFERENCES proyecto(id_proyecto)
        ON DELETE CASCADE
);

CREATE TABLE mision (
    id_mision SERIAL PRIMARY KEY,
    id_proyecto INT UNIQUE NOT NULL,
    descripcion TEXT NOT NULL,

    CONSTRAINT fk_mision_proyecto
        FOREIGN KEY (id_proyecto)
        REFERENCES proyecto(id_proyecto)
        ON DELETE CASCADE
);

CREATE TABLE valor (
    id_valor SERIAL PRIMARY KEY,
    id_proyecto INT NOT NULL,
    descripcion TEXT NOT NULL,

    CONSTRAINT fk_valor_proyecto
        FOREIGN KEY (id_proyecto)
        REFERENCES proyecto(id_proyecto)
        ON DELETE CASCADE
);

CREATE TABLE objetivo_estrategico (
    id_objetivo_est SERIAL PRIMARY KEY,
    id_proyecto INT NOT NULL,
    descripcion TEXT NOT NULL,

    CONSTRAINT fk_obj_est_proyecto
        FOREIGN KEY (id_proyecto)
        REFERENCES proyecto(id_proyecto)
        ON DELETE CASCADE
);

CREATE TABLE objetivo_especifico (
    id_objetivo_esp SERIAL PRIMARY KEY,
    id_objetivo_est INT NOT NULL,
    descripcion TEXT NOT NULL,

    CONSTRAINT fk_obj_esp_obj_est
        FOREIGN KEY (id_objetivo_est)
        REFERENCES objetivo_estrategico(id_objetivo_est)
        ON DELETE CASCADE
);