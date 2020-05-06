USE sisconnects; 
UPDATE integracao 
   SET after_execute = 'app_integracao_retorno_success_cta'
 WHERE before_detail = 'app_integracao_retorno_cta';