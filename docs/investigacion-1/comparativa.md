# Informe de Investigación 1: EasyPost API Testing

- **Herramienta:** Postman / Newman
- **Entorno:** Test Environment (Con Test API Key `EZTK...`)
- **Iteraciones de rendimiento:** 10 ejecuciones completadas con 0 errores.
- **Tiempo de respuesta promedio:** 208ms (Mínimo: 143ms, Máximo: 473ms).
- **Conclusión:** La API de EasyPost responde correctamente bajo las pruebas automatizadas por lotes, validando la estabilidad y conectividad de la integración.

# Shippo API Testing
- **Herramienta:** Postman / Newman
- **Entorno:** Test Environment (Con Test API Token shippo_test_api_token_...)
- **Iteraciones de rendimiento:** 10 ejecuciones completadas con 0 errores.
- **Tiempo de respuesta promedio:** 411ms (Mínimo: 97ms, Máximo: 1524ms).
- **Conclusión:** La API de Shippo responde correctamente bajo las pruebas automatizadas por lotes. Aunque presenta mayor latencia en su tiempo de respuesta máximo debido a la cotización en tiempo real con los transportistas, valida exitosamente la estabilidad y conectividad de la integración sin registrar caídas ni errores.