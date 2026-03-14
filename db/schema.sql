DROP DATABASE IF EXISTS owl_school;
CREATE DATABASE owl_school;
USE owl_school;

/* ==============================
   USUÁRIOS
   ============================== */
CREATE TABLE usuario (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  senha VARCHAR(100) NOT NULL,
  tipo_usuario ENUM('aluno','professor','responsavel','admin') NOT NULL
) ENGINE=InnoDB;

CREATE TABLE aluno (
  usuario_id INT PRIMARY KEY,
  CONSTRAINT fk_aluno_usuario 
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE professor (
  usuario_id INT PRIMARY KEY,
  telefone VARCHAR(30) NOT NULL UNIQUE,
  CONSTRAINT fk_professor_usuario 
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE responsavel (
  usuario_id INT PRIMARY KEY,
  telefone VARCHAR(30) NOT NULL UNIQUE,
  CONSTRAINT fk_responsavel_usuario 
    FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE aluno_responsavel (
  aluno_id INT NOT NULL,
  responsavel_id INT NOT NULL,
  PRIMARY KEY (aluno_id, responsavel_id),
  CONSTRAINT fk_ar_aluno       
    FOREIGN KEY (aluno_id)       REFERENCES aluno(usuario_id)       ON DELETE CASCADE,
  CONSTRAINT fk_ar_responsavel 
    FOREIGN KEY (responsavel_id) REFERENCES responsavel(usuario_id) ON DELETE CASCADE
) ENGINE=InnoDB;

/* ==============================
   TAREFAS / PROVAS / NOTAS
   ============================== */
CREATE TABLE tarefa (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(150) NOT NULL,
  descricao TEXT NOT NULL,
  data_entrega DATE NOT NULL
) ENGINE=InnoDB;

CREATE TABLE prova (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(120) NOT NULL,
  data DATE NOT NULL
) ENGINE=InnoDB;

CREATE TABLE prova_nota (
  prova_id INT NOT NULL,
  aluno_id INT NOT NULL,
  nota DECIMAL(5,2) NOT NULL,
  PRIMARY KEY (prova_id, aluno_id),
  CONSTRAINT fk_pn_prova FOREIGN KEY (prova_id) REFERENCES prova(id) ON DELETE CASCADE,
  CONSTRAINT fk_pn_aluno FOREIGN KEY (aluno_id) REFERENCES aluno(usuario_id) ON DELETE CASCADE
) ENGINE=InnoDB;

/* ==============================
   COMUNICADOS / ADVERTÊNCIAS
   ============================== */
CREATE TABLE comunicado (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(150) NOT NULL,
  corpo TEXT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE advertencia (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(120) NOT NULL,
  descricao TEXT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE aluno_advertencia (
  advertencia_id INT NOT NULL,
  aluno_id INT NOT NULL,
  PRIMARY KEY (advertencia_id, aluno_id),
  CONSTRAINT fk_aa_advertencia FOREIGN KEY (advertencia_id) REFERENCES advertencia(id) ON DELETE CASCADE,
  CONSTRAINT fk_aa_aluno       FOREIGN KEY (aluno_id)       REFERENCES aluno(usuario_id) ON DELETE CASCADE
) ENGINE=InnoDB;

/* ==============================
   HORÁRIOS / CHAMADAS
   ============================== */
CREATE TABLE horarios_aula (
  id INT AUTO_INCREMENT PRIMARY KEY,
  dia_semana ENUM('segunda','terca','quarta','quinta','sexta') NOT NULL,
  inicio TIME NOT NULL,
  fim TIME NOT NULL,
  disciplina TEXT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE chamada (
  id INT AUTO_INCREMENT PRIMARY KEY,
  data DATE NOT NULL
) ENGINE=InnoDB;

CREATE TABLE chamada_item (
  chamada_id INT NOT NULL,
  aluno_id   INT NOT NULL,
  status     ENUM('presente','falta') NOT NULL,
  PRIMARY KEY (chamada_id, aluno_id),
  CONSTRAINT fk_ci_chamada FOREIGN KEY (chamada_id) REFERENCES chamada(id) ON DELETE CASCADE,
  CONSTRAINT fk_ci_aluno   FOREIGN KEY (aluno_id)   REFERENCES aluno(usuario_id) ON DELETE CASCADE
) ENGINE=InnoDB;
