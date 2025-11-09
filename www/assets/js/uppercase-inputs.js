/**
 * Uppercase Inputs - Convierte automáticamente campos de texto a mayúsculas
 * Autor: ELECTROTEC
 * 
 * Este script aplica conversión automática a mayúsculas en campos de entrada
 * de texto y textareas, excepto aquellos que tienen tipos específicos o
 * el atributo data-no-uppercase.
 */

(function() {
    'use strict';

    /**
     * Aplica la funcionalidad de conversión a mayúsculas a un elemento
     * @param {HTMLElement} element - Input o textarea a procesar
     */
    function applyUppercase(element) {
        // Verificar si el elemento ya tiene el listener aplicado
        if (element.dataset.uppercaseApplied === 'true') {
            return;
        }

        // Tipos de input que NO deben convertirse a mayúsculas
        const excludedTypes = [
            'email', 'password', 'number', 'tel', 'date', 'time', 
            'datetime-local', 'month', 'week', 'url', 'search',
            'color', 'range', 'file', 'hidden', 'checkbox', 'radio'
        ];

        // Verificar si es un input con tipo excluido
        if (element.tagName === 'INPUT') {
            const inputType = (element.type || 'text').toLowerCase();
            if (excludedTypes.includes(inputType)) {
                return;
            }
        }

        // Verificar si tiene el atributo data-no-uppercase
        if (element.hasAttribute('data-no-uppercase')) {
            return;
        }

        // Verificar si el elemento es readonly o disabled
        if (element.readOnly || element.disabled) {
            return;
        }

        // Agregar listener para convertir a mayúsculas en tiempo real
        element.addEventListener('input', function(e) {
            const start = this.selectionStart;
            const end = this.selectionEnd;
            const originalLength = this.value.length;
            
            // Convertir a mayúsculas
            this.value = this.value.toUpperCase();
            
            // Restaurar la posición del cursor
            const newLength = this.value.length;
            const diff = newLength - originalLength;
            this.setSelectionRange(start + diff, end + diff);
        });

        // Marcar como procesado
        element.dataset.uppercaseApplied = 'true';
    }

    /**
     * Inicializa la conversión a mayúsculas en todos los elementos apropiados
     */
    function initializeUppercase() {
        // Seleccionar todos los inputs de texto y textareas
        const textInputs = document.querySelectorAll('input[type="text"], input:not([type]), textarea');
        
        textInputs.forEach(applyUppercase);
    }

    /**
     * Observa cambios en el DOM para aplicar uppercase a elementos dinámicos
     */
    function observeDOMChanges() {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        // Si el nodo agregado es un input o textarea
                        if ((node.tagName === 'INPUT' || node.tagName === 'TEXTAREA')) {
                            applyUppercase(node);
                        }
                        
                        // Buscar inputs y textareas dentro del nodo agregado
                        const inputs = node.querySelectorAll('input[type="text"], input:not([type]), textarea');
                        inputs.forEach(applyUppercase);
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    // Inicializar cuando el DOM esté listo
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initializeUppercase();
            observeDOMChanges();
        });
    } else {
        initializeUppercase();
        observeDOMChanges();
    }

    // Exponer función pública para aplicar manualmente si es necesario
    window.UppercaseInputs = {
        apply: applyUppercase,
        initialize: initializeUppercase
    };
})();
