<?php

namespace App\Console\Commands;

use App\Models\Book;
use App\Models\User;
use Illuminate\Console\Command;

class CreateFirstBook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'books:create-first';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria o primeiro livro com o conteúdo sobre psicologia financeira';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::first();
        
        if (!$user) {
            $this->error('Nenhum usuário encontrado. Crie um usuário primeiro.');
            return Command::FAILURE;
        }

        // Verificar se o livro já existe
        $existingBook = Book::where('title', 'LIKE', '%Mente de Quem Veio de Baixo%')
            ->where('user_id', $user->id)
            ->first();

        if ($existingBook) {
            $this->info('O livro já existe!');
            return Command::SUCCESS;
        }

        $content = <<<'CONTENT'
📖 PRÓLOGO
A VERDADE QUE NINGUÉM CONTA
Você nasceu num lar onde o dinheiro era contado, não planejado.
 Onde o fim do mês chegava antes do salário.
 Onde a conversa sobre finanças não era sobre investimentos, mas sobre como esticar o arroz, renegociar a conta de luz e fazer o que dava.
Você cresceu ouvindo:
 "Dinheiro não dá em árvore."
 "Quem nasceu pobre morre pobre."
 "Deus sabe o que faz."
 E sem perceber, essas frases viraram códigos dentro da sua mente — códigos de sobrevivência.
Não é que sua mãe não sonhasse. Ela apenas estava cansada.
 E não é que seu pai não quisesse dar mais. Ele apenas não aprendeu a lidar com o próprio vazio — e a bebida virou o alívio momentâneo para uma dor antiga, a de não se sentir capaz.
A verdade é que muitos de nós herdamos mais sentimentos do que bens.
 E é esse tipo de herança que determina como você pensa, sente e age diante do dinheiro.

🔹 O Silêncio Financeiro da Infância
Na infância, você aprendeu a calar o medo quando via sua mãe preocupada com as contas.
 Aprendeu a não pedir demais, porque pedir era peso.
 Aprendeu a se contentar, porque desejar muito era "coisa de quem não conhece a realidade".
Mas a realidade que te ensinaram não era o limite — era o reflexo das dores que vieram antes de você.
 A pobreza não é apenas a falta de dinheiro.
 Ela é a ausência de modelos mentais de prosperidade.
 É o acúmulo de gerações que viveram apagando incêndios, sem tempo de construir pontes.

🔹 A Mente de Sobrevivência
Quem vem de baixo aprende a pensar em hoje.
 E pensar em hoje é um mecanismo de defesa — é o cérebro tentando sobreviver.
 Você não poupa porque é irresponsável;
 você não investe porque o inconsciente diz que "amanhã pode dar errado".
 E se amanhã pode dar errado, o certo é viver o agora.
Esse é o ciclo invisível: o medo da falta faz gastar o que se tem.
 O gasto gera culpa.
 A culpa gera compensação.
 E a compensação leva à estagnação.
O nome disso é autossabotagem de origem emocional.
 E ela não se resolve com planilha — se resolve com consciência.

🔹 A Herança Invisível
Quem teve mãe doméstica e pai trabalhador de base carrega uma força absurda — mas também um código oculto:
"Trabalhar muito é o caminho da dignidade, mas enriquecer pode ser perigoso."
No fundo, existe uma culpa em prosperar mais do que os pais.
 Um medo inconsciente de "trair" as raízes, de parecer ingrato.
 Muitos sabotam o próprio sucesso porque, lá dentro, ainda querem ser aceitos pela origem.
Mas a verdade é outra:
 honrar a sua origem não é repetir a escassez; é curar o ciclo.
Você não veio para provar que é melhor que seus pais.
 Veio para ir além do que eles puderam — e fazer o que eles sonharam sem ter recursos.

🔹 O Preço da Ascensão
Subir dói.
 Porque toda ascensão exige abrir mão de algo — de crenças, de hábitos, às vezes de pessoas.
 E quem vem de baixo sente que, ao crescer, está deixando os outros para trás.
Mas a cura começa quando você entende que crescer não é trair, é expandir.
 Que riqueza não é ego, é estrutura.
 Que o dinheiro não te muda — ele revela quem você sempre foi, só que livre do medo.

🔹 O Chamado
Esse livro não é sobre enriquecer rápido.
 É sobre se tornar uma mente livre, consciente e digna da riqueza que constrói.
 É sobre entender que o dinheiro não é o vilão, mas também não é o herói.
 Ele é apenas um espelho da sua maturidade emocional.
Você vai entender que cada real tem um destino, cada escolha tem um preço e cada crença tem um poder.
 E que, no fim das contas, prosperar é uma forma de cura social — porque quando você muda sua história, muda a de quem vem depois.

🔹 A Jornada Começa
Prepare-se.
 Você vai revisitar memórias, quebrar padrões, enfrentar medos e aprender a pensar de um jeito que a sua antiga versão jamais pensou possível.
 Mas tudo isso com um único propósito:
 que a sua história de superação não seja só de resistência — seja de abundância.
🧩 CAPÍTULO 1
A MENTE DE QUEM VEIO DE BAIXO
A sociologia da escassez e o trauma da sobrevivência

Existem mentes treinadas para investir, e existem mentes treinadas para sobreviver.
 A diferença entre elas raramente é inteligência — é contexto.
Quem veio de baixo não aprendeu a lidar com abundância, porque nunca teve espaço para ela.
 A vida sempre foi uma sequência de "ou isso, ou aquilo".
 Ou paga a conta, ou compra o remédio.
 Ou come hoje, ou guarda para amanhã.
Esse "ou" se transforma, com o tempo, em um sistema mental de sobrevivência, e o cérebro passa a viver em modo de urgência constante — um modo que faz a pessoa correr, lutar, resistir, mas nunca relaxar o suficiente para planejar o futuro.

🔹 A Estrutura Mental da Sobrevivência
Na mente de quem nasceu em escassez, o cérebro aprendeu cedo que segurança não é ter — é não perder.
 Então, mesmo quando há dinheiro, o inconsciente opera em alerta: "isso não vai durar".
Esse padrão cria comportamentos previsíveis:
Gasto impulsivo após receber (como se o dinheiro fosse desaparecer).


Dificuldade em poupar ("pra que guardar se o mundo é incerto?").


Culpas ao se premiar ("não mereço tanto").


Autoexigência exagerada ("preciso provar que sou digno").


A pobreza ensina um tipo de inteligência adaptativa: saber se virar.
 Mas raramente ensina a projetar, porque o foco está sempre no agora.

🔹 A Sociologia da Escassez
Na base da pirâmide social, as pessoas não têm tempo para pensar — têm que reagir.
 Trabalham dobrado, dormem pouco e convivem com o medo constante da perda.
 Essa rotina cria uma cultura da resignação: "é assim mesmo", "um dia melhora", "Deus proverá".
A sociologia da escassez mostra que a desigualdade não é apenas material — é mental e emocional.
 Quem nasce em ambiente de luta herda não só a falta de recursos, mas a falta de referências.
 E quando não há modelos de crescimento, o cérebro aprende a normalizar o aperto.
O resultado é uma geração inteira emocionalmente cansada, que confunde cansaço com merecimento.
 E assim o ciclo continua: trabalha-se demais para sobreviver, mas sem construir alicerces para prosperar.

🔹 A Raiz Emocional
A mente de quem veio de baixo é um campo fértil de emoções fortes: orgulho, culpa, raiva, medo e amor.
 Mas esses sentimentos, quando não compreendidos, tornam-se mecanismos inconscientes que moldam cada decisão financeira.
O orgulho impede de pedir ajuda.


A culpa sabota o prazer de crescer.


A raiva alimenta comparações e impulsividade.


O medo paralisa quando é hora de investir.


O amor mal direcionado faz cuidar dos outros antes de cuidar de si.


Essas forças, que um dia foram escudos, se tornam grilhões invisíveis na vida adulta.
 E cada real gasto ou poupado carrega um significado emocional herdado da infância.

🔹 O Corpo e a Mente da Escassez
A neurociência explica que viver em modo de alerta constante aumenta os níveis de cortisol, o hormônio do estresse.
 Com o tempo, o cérebro se acostuma a funcionar sob tensão, e o "caos" passa a ser o estado normal.
 Quando tudo está bem, a pessoa sente estranhamento.
 É como se o silêncio fosse ameaçador.
É por isso que muitos criam problemas quando a vida começa a se estabilizar.
 A paz parece perigosa — e o inconsciente tenta recriar o ambiente de luta que ele reconhece como "seguro".
 É o vício emocional da escassez.

🔹 A Herança Familiar e Cultural
Crescer em um lar simples não é o problema.
 O problema é quando o modelo de mundo que vem desse lar permanece inquestionado.
Se você cresceu vendo o pai gastar tudo quando recebia, e a mãe dizer "dinheiro some", isso virou um script interno.
 E a mente repete scripts automaticamente.
A cultura também reforça isso: o "pobre honesto e trabalhador" é exaltado, mas o "rico" é visto com desconfiança.
 A mensagem oculta é: "Se eu prosperar, posso me tornar alguém ruim."
 Essa ideia trava gerações inteiras que têm medo de ter sucesso e perder a aceitação do grupo.

🔹 A Mente de Quem Está Despertando
Mas chega um ponto da jornada em que o sofrimento deixa de ser natural.
 A pessoa começa a perceber que o problema não é o salário — é o modelo mental.
É quando ela se pergunta:
"Por que, mesmo trabalhando tanto, ainda vivo apagando incêndios?"
 "Por que toda vez que guardo um dinheiro, algo acontece e eu perco?"
 "Por que sinto culpa em ter mais que meus pais?"
Essas perguntas não são fraqueza — são o primeiro sinal de consciência desperta.
 E é aqui que começa a virada: o momento em que você entende que dinheiro não é só economia, é emoção, é história e é identidade.

🔹 A Transição: Da Mente de Escassez à Mente de Construção
A primeira transformação não é financeira — é cognitiva.
 É quando você muda a pergunta.
 De "Como eu saio do aperto?" para "Como eu construo estabilidade?".
 De "Como eu ganho mais?" para "Como eu administro melhor?".
 De "Por que comigo?" para "O que eu posso aprender com isso?".
Essa mudança abre espaço para o raciocínio estratégico.
 O cérebro, quando deixa o modo de defesa, começa a liberar dopamina e serotonina — substâncias ligadas à motivação e à clareza.
 Ou seja: quando a mente relaxa, ela enriquece.

🔹 A Nova Linguagem do Dinheiro
Falar de dinheiro sem vergonha é um ato político e psicológico.
 Quem vem de baixo precisa reaprender a conversar sobre finanças com naturalidade, sem medo de parecer ambicioso.
Ambição não é ganância — é consciência de propósito.
 E propósito é o que transforma esforço em direção.
O dinheiro começa a se multiplicar não quando você ganha mais, mas quando ele deixa de ser um tabu.
 Quando você consegue olhar para o extrato bancário sem sentir culpa, e para o futuro sem sentir pânico.

🔹 Conclusão: O Mapa da Consciência Financeira
A mente de quem veio de baixo carrega uma sabedoria que o rico não tem: resiliência.
 Mas ela precisa ser redirecionada.
 O que antes servia para sobreviver, agora precisa servir para crescer.
E o crescimento começa quando você aceita três verdades:
Você não é o que viveu, é o que faz com o que viveu.


Riqueza é consequência de consciência, não de sorte.


O passado foi a escola. O futuro é o projeto.
💭 CAPÍTULO 2
PSICOLOGIA DA AUTOSSABOTAGEM FINANCEIRA
Por que quem trabalha tanto ainda não enriquece

Você já se perguntou por que, mesmo sabendo o que precisa ser feito, parece que "algo" dentro de você impede?
 Por que, mesmo quando sobra dinheiro, ele "desaparece"?
 Ou por que você sente ansiedade quando começa a prosperar, como se o sucesso fosse frágil demais?
Isso tem nome: autossabotagem financeira.
 E ela não nasce da preguiça, nem da falta de conhecimento.
 Ela nasce da estrutura emocional construída lá atrás, nas experiências de carência, culpa e medo.
O cérebro, quando não curado, reproduz a realidade que conhece.
 E se ele conheceu escassez, ele cria situações que a mantenham — mesmo que você diga que quer o contrário.

🔹 O Que É Autossabotagem Financeira?
É o conjunto de atitudes, emoções e decisões que inconscientemente te afastam da estabilidade ou do crescimento financeiro.
 Ela se manifesta através de atrasos, impulsos, esquecimentos, exageros, procrastinação e repetições.
 Em resumo:
É quando a mente tenta proteger você do que ela acha perigoso — inclusive da riqueza.

🔹 Por Que o Cérebro Faz Isso?
O cérebro é um órgão de economia de energia.
 Ele prefere o conhecido ao novo, mesmo que o conhecido doa.
 Se a sua mente associou prosperidade a risco, rejeição, inveja ou perda, ela fará de tudo para te manter "seguro" — ainda que isso signifique continuar no aperto.
Autossabotagem, portanto, é proteção mal interpretada.
 Seu inconsciente não quer te destruir. Ele quer te manter onde "é seguro".
 O problema é que a segurança da infância não é a liberdade da vida adulta.

🔹 Os 50 Principais Motivos de Autossabotagem Financeira
A seguir estão os 50 principais gatilhos e padrões que sabotam quem veio de baixo, divididos por áreas emocionais.
 Cada item inclui o mecanismo psicológico e o efeito financeiro real.

🔸 1. Crença de que dinheiro é sujo ou perigoso
Você viu pessoas ricas sendo julgadas.
 Seu inconsciente aprendeu que ter é ser "ganancioso".
 👉 Resultado: inconscientemente evita prosperar para continuar "limpo".
🔸 2. Medo de repetir erros dos pais
Ao crescer vendo descontrole ou dívidas, você foge de lidar com finanças.
 👉 Resultado: ausência de controle para evitar reviver a dor.
🔸 3. Necessidade de provar valor
Trabalhar demais, gastar para mostrar sucesso.
 👉 Resultado: desgaste físico e emocional, com zero construção.
🔸 4. Crença de que "dinheiro não traz felicidade"
Você desvaloriza o papel do dinheiro como ferramenta.
 👉 Resultado: negligência e repulsa à gestão financeira.
🔸 5. Medo de ser rejeitado por crescer
A culpa por "deixar os outros para trás" impede avanços.
 👉 Resultado: autolimitação e sabotagem de oportunidades.
🔸 6. Autoimagem pobre
Você se vê como "quem se vira", não como "quem lidera".
 👉 Resultado: aceita menos, cobra menos, exige menos.
🔸 7. Falta de merecimento
Você sente que não é digno de conforto.
 👉 Resultado: quando prospera, cria crises para voltar ao padrão antigo.
🔸 8. Vício emocional no caos
A paz parece estranha, o problema parece "vida normal".
 👉 Resultado: decisões impulsivas quando tudo está bem.
🔸 9. Medo do sucesso
O inconsciente associa sucesso à perda de liberdade ou solidão.
 👉 Resultado: evita grandes movimentos, mesmo com capacidade.
🔸 10. Medo do fracasso
Não tenta, para não falhar.
 👉 Resultado: estagnação.

🔹 Traumas e Modelos Familiares
11. Pai provedor ausente emocionalmente
Aprendeu que dinheiro vem com frieza.
 👉 Resultado: confunde estabilidade com distanciamento.
12. Mãe guerreira e sobrecarregada
Associa esforço extremo a valor.
 👉 Resultado: não aceita facilidade — tudo precisa ser difícil.
13. Brigas por dinheiro em casa
O cérebro liga "dinheiro = conflito".
 👉 Resultado: evita falar de finanças, foge de negociações.
14. Falta de diálogo sobre finanças
Nunca aprendeu a planejar.
 👉 Resultado: ansiedade e vergonha em lidar com números.
15. Vergonha da origem
Esconde suas raízes e tenta parecer "bem".
 👉 Resultado: consumo de aparência.

🔹 Padrões Comportamentais
16. Gastar para aliviar emoções
O prazer da compra substitui o conforto emocional.
 👉 Resultado: dívidas e arrependimento.
17. Adiar decisões financeiras
Procrastinação disfarçada de "cautela".
 👉 Resultado: oportunidades perdidas.
18. Excesso de generosidade
Acha que doar tudo é ser bom.
 👉 Resultado: ajuda todos, menos a si mesmo.
19. Comparação constante
Compra o que o outro tem para se sentir válido.
 👉 Resultado: vida baseada em competição emocional.
20. Falta de limites com a família
Sustenta parentes por culpa.
 👉 Resultado: nunca avança.

🔹 Crenças de Identidade
21. "Rico é diferente de mim."
Cria barreira simbólica.
 👉 Resultado: se exclui das oportunidades.
22. "Deus proverá, não preciso me preocupar."
Usa a fé como fuga da responsabilidade.
 👉 Resultado: não se organiza.
23. "Nunca sobra."
Transforma falta em profecia autorrealizável.
 👉 Resultado: o dinheiro realmente nunca sobra.
24. "Eu sou azarado."
Vitimismo disfarçado de crença espiritual.
 👉 Resultado: não assume protagonismo.
25. "Trabalhar muito é virtude."
Confunde esforço com eficiência.
 👉 Resultado: cansaço crônico e resultados pequenos.

🔹 Bloqueios Emocionais
26. Medo de olhar para dívidas
Foge dos extratos para evitar culpa.
 👉 Resultado: perda de controle e juros altos.
27. Falta de confiança em si
Dúvida das próprias decisões financeiras.
 👉 Resultado: paralisação e dependência de terceiros.
28. Baixa tolerância à frustração
Desiste fácil quando algo dá errado.
 👉 Resultado: instabilidade crônica.
29. Vergonha de pedir desconto
Associa economia a humilhação.
 👉 Resultado: paga caro por orgulho.
30. Culpa ao se premiar
Sente-se errado ao desfrutar o que conquistou.
 👉 Resultado: sabotagem da própria alegria.

🔹 Padrões de Relacionamento
31. Casar por carência
Busca segurança em vez de parceria.
 👉 Resultado: decisões financeiras baseadas em medo.
32. Sustentar para ser amado
Compra amor com dinheiro.
 👉 Resultado: relacionamentos desequilibrados.
33. Repetir o padrão dos pais
Relações marcadas por escassez e dependência.
 👉 Resultado: reprodução do mesmo ciclo.
34. Inveja inconsciente dos bem-sucedidos
Deseja o sucesso, mas julga quem tem.
 👉 Resultado: conflito interno e rejeição do próprio progresso.
35. Confundir humildade com pequenez
Acha que crescer é ser arrogante.
 👉 Resultado: reprime ambição legítima.

🔹 Comportamentos Financeiros Diretos
36. Falta de planejamento mensal
Não sabe quanto entra e sai.
 👉 Resultado: ansiedade e endividamento.
37. Não ter reserva de emergência
Vive vulnerável a imprevistos.
 👉 Resultado: ciclo de estresse e dependência.
38. Falta de metas financeiras
Sem direção, qualquer gasto parece justificável.
 👉 Resultado: desperdício crônico.
39. Resistência a estudar finanças
Acha chato, técnico demais.
 👉 Resultado: continua dependente dos outros.
40. Falta de rotina de revisão
Nunca reavalia hábitos e contratos.
 👉 Resultado: perde dinheiro sem perceber.

🔹 Padrões de Autopercepção e Ambiente
41. Cercar-se de pessoas negativas
O grupo reforça a mediocridade.
 👉 Resultado: autoimagem enfraquecida.
42. Ambientes desorganizados
Casa ou trabalho caótico refletem mente confusa.
 👉 Resultado: dificuldade em ter clareza financeira.
43. Não celebrar conquistas
Não consolida o sucesso emocional.
 👉 Resultado: volta sempre à estaca zero.
44. Fuga da responsabilidade
Culpa o governo, o mercado, o destino.
 👉 Resultado: perde poder pessoal.
45. Falta de paciência com o tempo do crescimento
Quer resultados imediatos.
 👉 Resultado: abandona estratégias antes de florescerem.

🔹 Padrões de Desmotivação e Falta de Sentido
46. Trabalhar sem propósito
Sem significado, o esforço vira fardo.
 👉 Resultado: gasto emocional e financeiro desordenado.
47. Falta de visão de futuro
Não imagina o amanhã em detalhes.
 👉 Resultado: o cérebro não cria direção.
48. Ignorar o próprio corpo e saúde
Cansaço e doença drenam energia e foco.
 👉 Resultado: decisões ruins e improdutividade.
49. Falta de gratidão
Foco no que falta, não no que já tem.
 👉 Resultado: vibração constante de escassez.
50. Medo de recomeçar
Fracasso vira sentença, não aprendizado.
 👉 Resultado: paralisação após erros.

🔹 A Solução: Cura e Consciência
A autossabotagem não se vence com força de vontade.
 Ela se dissolve com consciência, autocompaixão e sistema.
Consciência: identificar o padrão.


Autocompaixão: entender que o padrão veio de dor, não de defeito.


Sistema: criar estrutura externa (planilhas, metas, regras) para reeducar o cérebro.


O processo é simples, mas exige disciplina emocional:
toda vez que sentir medo, em vez de reagir, pergunte:
 "Isso é uma decisão real ou uma defesa antiga?"
Essa pergunta sozinha muda destinos.

Capítulo 3 — O Gerenciamento da Vida Financeira: Cada Real Conta
1. O dinheiro não some — ele é expulso
Muitos acreditam que "o dinheiro vai embora rápido".
 Mas, na verdade, o dinheiro é expulso quando não tem função definida.
 Quem veio de baixo foi acostumado a reagir ao dinheiro: quando entra, gasta-se; quando acaba, sofre-se.
 Essa mentalidade emocional precisa ser substituída por uma mentalidade gerencial:
"Cada real que entra precisa de uma missão antes mesmo de existir."
Como fazer isso na prática
Tenha um plano fixo de divisão do dinheiro:


60% para custos de vida (moradia, alimentação, transporte, contas básicas).


20% para metas e projetos (empresa, melhorias pessoais, casa, cursos).


10% para reserva de emergência.


10% para lazer e recompensas (essencial para o equilíbrio mental).


Anote tudo. O que não é escrito, o cérebro ignora.
 Use papel, planilha, aplicativo — o que for, mas escreva onde cada centavo está indo.



2. Pensar como gestor, não como sobrevivente
Quem vem de uma origem humilde cresceu vendo o dinheiro ser sobrevivência, não gestão.
 Mas prosperar exige pensar como administrador — mesmo que você ainda esteja com pouco.
"A pobreza ensina a sobreviver, mas a prosperidade exige método."
Método da tríade financeira
Planejar: saber o que virá no mês (entradas e saídas previstas).


Executar: gastar apenas o que foi planejado.


Revisar: toda semana, ajustar o que saiu do controle.


Crie o hábito do domingo financeiro — 15 minutos por semana para revisar e decidir:
O que foi gasto?


O que foi desnecessário?


O que vai mudar na próxima semana?


Isso forma um cérebro financeiro ativo, e não passivo.

3. A casa, o negócio e a família: três pilares de um mesmo plano
A desorganização vem quando tratamos cada parte da vida como um mundo separado.
 Mas para quem quer crescer, tudo é um mesmo sistema financeiro com objetivos conectados:
Pilar
Missão
Pensamento Ideal
Casa
Segurança e base emocional
"Minha casa é o reflexo da minha estrutura mental."
Negócio
Multiplicador de valor
"Minha empresa deve ser uma extensão da minha visão, não da minha pressa."
Família
Razão e propósito
"Minha família é a causa, mas também deve ser parte da construção."

Como pensar cada um:
Casa: mantenha custos fixos sustentáveis. Conforto é importante, mas não pode ser fardo.
 Evite comprometer mais de 30% da renda com aluguel ou financiamento.
 Transforme seu lar em ambiente produtivo — não em espaço de fuga.


Negócio: não retire todo o lucro para consumo.
 Crie o hábito de reinvestir pelo menos 20% do faturamento líquido.
 O negócio deve alimentar a casa, mas a casa não pode drenar o negócio.


Família: envolva todos na mentalidade financeira.
 Converse sobre planos, explique decisões.
 A maior falha de quem veio de baixo é querer proteger a família da verdade financeira, e isso gera desconexão e sabotagem.
 Prosperar é educar, não esconder.



4. Cada plano precisa ter data, valor e propósito
Um plano sem data é sonho.
 Um plano sem valor é confusão.
 Um plano sem propósito é peso.
"O que é medido é gerenciável; o que é vago se perde no vento."
Crie planos de crescimento em camadas:
Curto prazo (0–6 meses): pagar dívidas, estabilizar contas, criar reserva.


Médio prazo (6–24 meses): aumentar renda, investir no negócio, melhorar moradia.


Longo prazo (2–10 anos): casa própria, independência financeira, legado familiar.


Escreva em uma folha visível:
 "Eu administro a minha história."
 E revise seus planos a cada trimestre — isso reprograma o cérebro para o controle e o progresso.

5. A relação emocional com o dinheiro
O dinheiro é o espelho da mente.
 Ele não muda por planilha — muda quando o sentido interno muda.
 Quem veio de baixo associa dinheiro a luta, sacrifício e culpa.
 Mas o dinheiro não é moral: é uma ferramenta.
 A verdadeira libertação começa quando você para de julgar o dinheiro e começa a comandar o dinheiro.
"Enquanto o dinheiro for inimigo, ele nunca ficará perto de você."
Comece a agradecer cada entrada, mesmo que pequena.
 Isso ativa o senso de abundância consciente, que muda o padrão de escassez.
 Não é misticismo — é neuroplasticidade aplicada à psicologia financeira.

6. Conclusão do capítulo
Gerenciar a vida financeira não é tarefa de quem tem muito;
 é de quem decidiu crescer de forma lúcida.
A mente pobre sonha com o prêmio;
 a mente rica cria o sistema.
"Cada real que você honra é um voto a favor da sua liberdade."
Nos próximos capítulos, o livro entra na psicologia do crescimento, mostrando como sustentar a prosperidade, como lidar com a culpa de subir na vida, e como transformar disciplina em identidade.
CONTENT;

        $book = Book::create([
            'user_id' => $user->id,
            'title' => 'A Mente de Quem Veio de Baixo: Psicologia Financeira e Autossabotagem',
            'content' => $content,
        ]);

        $this->info("✅ Primeiro livro criado com sucesso!");
        $this->info("📖 Título: {$book->title}");
        $this->info("👤 Usuário: {$user->name} ({$user->email})");

        return Command::SUCCESS;
    }
}
