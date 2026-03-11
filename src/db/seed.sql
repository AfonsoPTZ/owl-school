USE owl_school;



/* ==============================
   USUÁRIOS
   ============================== */
INSERT INTO usuario (nome, email, senha, tipo_usuario) VALUES
  ('João da Silva',   'joao.aluno@teste.com',   '123456', 'aluno'),
  ('Ana Santos',      'ana.aluno@teste.com',    '123456', 'aluno'),
  ('Bruno Lima',      'bruno.aluno@teste.com',  '123456', 'aluno'),
  ('Carlos Pereira',  'carlos.resp@teste.com',  '123456', 'responsavel'),
  ('Paulo Santos',    'paulo.resp@teste.com',   '123456', 'responsavel'),
  ('Cláudia Lima',    'claudia.resp@teste.com', '123456', 'responsavel'),
  ('Maria Souza',     'maria.prof@teste.com',   '123456', 'professor'),
  ('Admin Master',    'admin@teste.com',        '123456', 'admin');



/* ==============================
   ALUNOS (referenciam usuario.id)
   ============================== */
INSERT INTO aluno (usuario_id) VALUES
  (1),
  (2),
  (3);



/* ==============================
   RESPONSÁVEIS (referenciam usuario.id)
   ============================== */
INSERT INTO responsavel (usuario_id, telefone) VALUES
  (4, '(41) 99999-1234'),
  (5, '(41) 98888-2222'),
  (6, '(41) 97777-3333');



/* ==============================
   PROFESSORES (referenciam usuario.id)
   ============================== */
INSERT INTO professor (usuario_id, telefone) VALUES
  (7, '(41) 98888-7777');



/* ==============================
   VÍNCULO ALUNO-RESPONSÁVEL
   (aluno.usuario_id x responsavel.usuario_id)
   ============================== */
INSERT INTO aluno_responsavel (aluno_id, responsavel_id) VALUES
  (1, 4),
  (2, 5),
  (3, 6);



/* ==============================
   HORÁRIOS DE AULA
   ============================== */
INSERT INTO horarios_aula (dia_semana, inicio, fim, disciplina) VALUES
  ('segunda', '07:30:00', '08:20:00', 'Matemática'),
  ('segunda', '08:20:00', '09:10:00', 'Português'),
  ('segunda', '09:20:00', '10:10:00', 'Ciências'),
  ('segunda', '10:10:00', '11:00:00', 'História'),
  ('segunda', '11:00:00', '11:50:00', 'Geografia'),

  ('terca', '07:30:00', '08:20:00', 'Inglês'),
  ('terca', '08:20:00', '09:10:00', 'Matemática'),
  ('terca', '09:20:00', '10:10:00', 'Português'),
  ('terca', '10:10:00', '11:00:00', 'Educação Física'),
  ('terca', '11:00:00', '11:50:00', 'Artes'),

  ('quarta', '07:30:00', '08:20:00', 'História'),
  ('quarta', '08:20:00', '09:10:00', 'Geografia'),
  ('quarta', '09:20:00', '10:10:00', 'Matemática'),
  ('quarta', '10:10:00', '11:00:00', 'Português'),
  ('quarta', '11:00:00', '11:50:00', 'Ciências'),

  ('quinta', '07:30:00', '08:20:00', 'Matemática'),
  ('quinta', '08:20:00', '09:10:00', 'Inglês'),
  ('quinta', '09:20:00', '10:10:00', 'Português'),
  ('quinta', '10:10:00', '11:00:00', 'História'),
  ('quinta', '11:00:00', '11:50:00', 'Educação Física'),

  ('sexta', '07:30:00', '08:20:00', 'Geografia'),
  ('sexta', '08:20:00', '09:10:00', 'Matemática'),
  ('sexta', '09:20:00', '10:10:00', 'Português'),
  ('sexta', '10:10:00', '11:00:00', 'Ciências'),
  ('sexta', '11:00:00', '11:50:00', 'Artes');
