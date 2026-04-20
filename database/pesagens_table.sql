-- Criação da tabela de pesagens
CREATE TABLE pesagens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    coleta_id BIGINT UNSIGNED NOT NULL,
    usuario_id BIGINT UNSIGNED NOT NULL,
    tipo_id BIGINT UNSIGNED NOT NULL,
    peso DECIMAL(8,2) NOT NULL,
    quantidade INT NOT NULL DEFAULT 1,
    peso_unitario DECIMAL(8,2) NULL,
    data_pesagem DATETIME NOT NULL,
    observacoes TEXT NULL,
    local_pesagem VARCHAR(255) NULL,
    conferido BOOLEAN NOT NULL DEFAULT FALSE,
    usuario_conferencia_id BIGINT UNSIGNED NULL,
    data_conferencia DATETIME NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    
    -- Chaves estrangeiras
    FOREIGN KEY (coleta_id) REFERENCES coletas(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (tipo_id) REFERENCES tipos(id),
    FOREIGN KEY (usuario_conferencia_id) REFERENCES usuarios(id),
    
    -- Índices para melhor performance
    INDEX idx_pesagens_coleta_tipo (coleta_id, tipo_id),
    INDEX idx_pesagens_data (data_pesagem),
    INDEX idx_pesagens_usuario (usuario_id)
);

-- Inserir registro na tabela migrations para controle do Laravel
INSERT INTO migrations (migration, batch) VALUES ('2024_01_01_000011_create_pesagens_table', 1);
