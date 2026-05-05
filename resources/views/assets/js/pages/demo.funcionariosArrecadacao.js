$(document).ready(function () {
    // URL da API que retorna os funcionários
    var apiUrl = 'http://localhost:8888/sigecm/api/withdraw-funcionarios';
 
    // Carregar os dados dos funcionários da API assim que a página for carregada
    $.ajax({
        url: apiUrl, // URL da sua API
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            // Verificar se a resposta é válida
            if (Array.isArray(data) && data.length > 0) {
                // Mapear os dados para extrair os nomes dos funcionários e os dados completos
                var funcionarios = data.map(function (funcionario) {
                    return {
                        id: funcionario.id,
                        name: funcionario.name,
                        codigo_departamento: funcionario.codigo_departamento,
                        departamento: funcionario.departamento,
                        subunidade: funcionario.subunidade,
                        fotografia: funcionario.fotografia,
                        genero: funcionario.genero,
                        patente: funcionario.patente,  
                        celular: funcionario.celular, 
                        criado_em: funcionario.criado_em // Supondo que esse campo seja disponibilizado
                    };
                });
 
                // Inicializar o Typeahead com os dados recebidos
                $("#prefetch").typeahead(
                    { hint: true, highlight: true, minLength: 1 },
                    {
                        name: "funcionarios",
                        source: function (query, process) {
                            var results = [];
                            var substrRegex = new RegExp(query, "i");
                            // Filtrar os resultados que correspondem à pesquisa
                            $.each(funcionarios, function (index, funcionario) {
                                if (substrRegex.test(funcionario.name)) {
                                    results.push(funcionario);
                                }
                            });
                            process(results);
                        },
                        display: function (funcionario) {
                            return funcionario.name; // Exibir o nome no input
                        }
                    }
                );
 
                // Evento de seleção do nome
                $('#prefetch').on('typeahead:selected', function (e, funcionario) {
                    // Preencher os campos visíveis
                    $("#codigo_funcionario").val(funcionario.id);
                    $("#funcionario-nome").val(funcionario.name);
                    $("#patente").val(funcionario.patente).trigger('change');
                    $("#gender").val(funcionario.genero).trigger('change');
                    $("#departamento").val(funcionario.codigo_departamento).trigger('change');
                    $("#subunidade").val(funcionario.subunidade).trigger('change');
                    $("#celular").val(funcionario.celular);

                });
            } else {
                console.error('Erro: Dados de funcionários não encontrados.');
            }
        },
        error: function (xhr, status, error) {
            console.error('Erro na requisição à API:', error);
        }
    });
});
