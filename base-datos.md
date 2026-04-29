## 📊 Diagrama Entidad-Relación

```mermaid
erDiagram

PERSONA {
  int id_persona PK
  string nombre
  string email
}

PROYECTO {
  int id_proyecto PK
  string nombre
  int creador_id
}

PROYECTO_MIEMBRO {
  int id PK
  int id_proyecto
  int id_persona
  string rol
}

MISION {
  int id_mision PK
  int id_proyecto
  string descripcion
}

VISION {
  int id_vision PK
  int id_proyecto
  string descripcion
}

VALOR {
  int id_valor PK
  int id_proyecto
  string descripcion
}

OBJETIVO_ESTRATEGICO {
  int id_objetivo_est PK
  int id_mision
  string descripcion
}

OBJETIVO_ESPECIFICO {
  int id_objetivo_esp PK
  int id_objetivo_est
  string descripcion
}

FODA {
  int id_foda PK
  int id_proyecto
}

FORTALEZA {
  int id_fortaleza PK
  int id_foda
  string descripcion
}

DEBILIDAD {
  int id_debilidad PK
  int id_foda
  string descripcion
}

OPORTUNIDAD {
  int id_oportunidad PK
  int id_foda
  string descripcion
}

AMENAZA {
  int id_amenaza PK
  int id_foda
  string descripcion
}

AUTODIAGNOSTICO {
  int id_autodiagnostico PK
  int id_proyecto
}

PREGUNTA {
  int id_pregunta PK
  string texto
}

RESPUESTA_AUTODIAGNOSTICO {
  int id PK
  int id_autodiagnostico
  int id_pregunta
  int valor "0-5"
}

REFLEXION_AUTODIAGNOSTICO {
  int id_reflexion PK
  int id_autodiagnostico
  string contenido
}

%% RELACIONES

PERSONA ||--o{ PROYECTO : crea
PERSONA ||--o{ PROYECTO_MIEMBRO : participa
PROYECTO ||--o{ PROYECTO_MIEMBRO : tiene

PROYECTO ||--|| MISION : tiene
PROYECTO ||--|| VISION : tiene
PROYECTO ||--o{ VALOR : tiene

MISION ||--o{ OBJETIVO_ESTRATEGICO : genera
OBJETIVO_ESTRATEGICO ||--o{ OBJETIVO_ESPECIFICO : tiene

PROYECTO ||--|| FODA : tiene
FODA ||--o{ FORTALEZA : contiene
FODA ||--o{ DEBILIDAD : contiene
FODA ||--o{ OPORTUNIDAD : contiene
FODA ||--o{ AMENAZA : contiene

PROYECTO ||--|| AUTODIAGNOSTICO : tiene
AUTODIAGNOSTICO ||--o{ RESPUESTA_AUTODIAGNOSTICO : responde
PREGUNTA ||--o{ RESPUESTA_AUTODIAGNOSTICO : define
AUTODIAGNOSTICO ||--o{ REFLEXION_AUTODIAGNOSTICO : genera