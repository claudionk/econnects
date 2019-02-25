<div class="layout-app">
    <!-- row -->
    <div class="row row-app">
        <!-- col -->
        <div class="col-md-12">
            <div class="section-header">
                <ol class="breadcrumb">
                    <li class="active"><?php echo app_recurso_nome();?></li>
                </ol>
            </div>

            <!-- col-separator -->
            <div class="col-separator col-separator-first col-unscrollable">
                <div class="row">
                    <div class="col-md-12">
                        <?php $this->load->view('admin/partials/messages'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link" id="dash-tab" data-toggle="tab" href="#dash" role="tab" aria-controls="dash" aria-selected="false">DASHBOARDS</a>
                                </li>
                                <li class="nav-item active">
                                    <a class="nav-link active" id="processamento-tab" data-toggle="tab" href="#processamento" role="tab" aria-controls="processamento" aria-selected="true" aria-expanded="true">Processamento</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="historico-tab" data-toggle="tab" href="#historico" role="tab" aria-controls="historico" aria-selected="false">HISTÓRICO</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade" id="dash" role="tabpanel" aria-labelledby="dash-tab">
                                    <ul>
                                        <li>
                                            <p>Dashboard Executivo</p>
                                            <p>O dashboard executivo permite a visualização de grande quantidade de informações, devidamente estratificadas e formatadas, de modo a facilitar o processo de tomada de decisões. Esse painel utiliza recursos visuais e gráficos para facilitar a compreensão geral. É muito útil para monitorar indicadores e KPIs, possibilitando uma análise completa sobre todos os processos da empresa. É o chamado dashboard de negócio e utilizado principalmente para facilitar e agilizar a tomada de decisões. Para que isso aconteça é importante que os dados gerados sejam atualizados em tempo real, pois com a competitividade dos mercados, é inaceitável que se utilizem dados ultrapassados para a tomada de decisão.</p>

                                            <p>Normalmente estão baseados nas principais metologias de gestão, como, por exemplo, o BSC ou Balanced Scorecard. Através deste tipo de dashboard, todos os executivos podem ter acesso a dados importantes para a avaliação de novas oportunidades de melhoria ou ainda, de expansão do negócio. A integração das informações propicia uma gestão mais efetiva e ágil, capaz de alinhar rapidamente, estratégias, ações e decisões. O dashboard executivo é capaz de simplificar todo o fluxo de dados, reduzindo burocracias e agregando valor.</p>

                                             
                                            <p>exemplos de dashboards</p>

                                            <p>Dashboard do OpMon com visão de vendas de produtos</p>
                                            <img src="https://www.opservices.com.br/wp-content/uploads/2017/05/Varejo_Vendas_Acumuladas-1024x597.png">

                                             
                                            <p>Para saber mais, baixe os nossos whitepapers: Catálogo de Dashboards com exemplos reais e o Monitoramento Comportamental do Negócio.</p>
                                        </li>
                                        <li>
                                            <p>Dashboard Analítico</p>
                                            <p>O dashboard analítico é preparado para oferecer informações mais detalhadas e é usado para traçar tendências em relação aos objetivos corporativos predeterminados, ou seja, para avaliar se processos e projetos estão evoluindo de acordo com as expectativas. Com dados constantemente atualizados, é possível perceber rapidamente os resultados de ações internas, bem como as reações do mercado em relação a produtos ou serviços lançados. É uma maneira de mensurar os impactos causados por cada decisão tomada, sendo possível assim, interferir e corrigir os desvios com maior rapidez, evitando desperdícios e prejuízos. Um exemplo de ferramenta que produz esses dashboards analíticos é o Google Analytics, uma poderosa ferramenta da Google para visualizar indicadores da área de marketing, com dados desde acessos ao site até o funil de vendas on-line com indicadores sobre taxas de conversão e mapeamento de origem do tráfego.</p>

                                     
                                            <p>Dashboard Analítico - Google Analytics</p>

                                            <p>Dashboard do Google Analytics</p>

                                            <img src="https://www.opservices.com.br/wp-content/uploads/2018/04/Dashboard-Anal%C3%ADtico.png">
                                        </li>
                                     

                                        <li>
                                            <p>Dashboard Operacional</p>
                                            <p>O dashboard operacional é utilizado diretamente pelas equipes de trabalho, com foco em determinados processos, possibilitando análises específicas. Esses dados servem para identificar gargalos e etapas críticas da operação, auxiliando na correção de problemas pontuais e tendências negativas. Facilitam a comunicação com simplicidade e ainda permitem a interação e a atualização de todos os profissionais envolvidos. Dentre os exemplos de dashboards operacionais podem estar a visualização de indicadores com informações sobre a infraestrutura de TI que suporta o negócio.</p>

                                             
                                            <p>Dashboard de Infraestrutura de TI - Datacenter</p>

                                            <p>Dashboard do OpMon com visão da infraestrutura de Datacenter</p>

                                            <img src="https://www.opservices.com.br/wp-content/uploads/2018/05/Dashboard-Monitoramento-Infraestrutura-de-TI.png">
                                        </li>
                                             
                                        <li>

                                            <p>Dashboard de gerenciamento de projetos</p>
                                            <p>Através deste dashboard é possível acompanhar todo o gerenciamento do projeto, incluindo a evolução de cada estágio, o cronograma, as atividades previstas, as interdependências e o percentual de conclusão do empreendimento. Nesse painel de controle também são combinadas algumas técnicas, como formatação condicional, referências e gráficos, que permitem o fácil entendimento de todas essas informações, reduzindo os riscos e otimizando os recursos disponíveis.</p>

                                             
                                            <p>Dashboard Gerenciamento de Projetos de TI</p>

                                            <p>Dashboard de Projetos com Software Jira</p>

                                            <img src="https://www.opservices.com.br/wp-content/uploads/2018/04/Dashboard-Projetos-1024x501.png">
                                        </li>
                                             
                                        <li>
                                            <p>Dashboard de atendimento ao cliente via call center</p>
                                            <p>Com a adoção de um dashboard de atendimento ao cliente, o gestor é capaz de monitorar todo o call center de uma empresa. Trata-se de uma solução bastante versátil, estruturada para elaborar diversas estatísticas e relatórios comparativos, envolvendo dados como número de chamadas por período e colaborador, duração de cada atendimento, casos resolvidos ou aguardando outras providências, natureza do contato e avaliação de satisfação do cliente, apenas para citar algumas funcionalidades.</p>

                                            <img src="https://www.opservices.com.br/wp-content/uploads/2017/05/Telecom_IndicadoresServiceDesk-1024x597.png">
                                        </li>

                                        <li>

                                            <p>Dashboard do OpMon com visão de indicadores de Service Desk</p>

                                             
                                            <p>Resumindo: os dashboards são painéis de informações que mostram de forma gráfica os indicadores de TI e processos de negócio de uma empresa. Esses painéis contém indicadores, gráficos, relatórios e filtros específicos apresentando as informações mais importantes alinhadas em uma única tela para facilitar o acompanhamento do negócio e tomada de decisões.</p>
                                        </li>
                                </div>
                                <div class="tab-pane fade  active in" id="processamento" role="tabpanel" aria-labelledby="processamento-tab">
                                    <br>
                                    <div class="accordion" id="accordionProcessamento">
                                        <div class="card">
                                            <div class="card-header" id="headingOne">
                                                <h5 class="mb-0">
                                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                    VENDAS
                                                    </button>
                                                </h5>
                                            </div>

                                            <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionProcessamento">
                                                <div class="card-body">
                                                Processamento de Vendas
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header" id="headingTwo">
                                                <h5 class="mb-0">
                                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                CTA
                                                </button>
                                                </h5>
                                            </div>
                                            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionProcessamento">
                                                <div class="card-body">
                                                    CTA
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header" id="headingThree">
                                                <h5 class="mb-0">
                                                <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                RETORNOS
                                                </button>
                                                </h5>
                                            </div>
                                            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionProcessamento">
                                                <div class="card-body">
                                                    <?php echo '<pre>'; print_r($retorno); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="historico" role="tabpanel" aria-labelledby="historico-tab">
                                ...
                                </div>
                            </div>        
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- <script type="text/javascript">$('.collapse').collapse();</script> -->