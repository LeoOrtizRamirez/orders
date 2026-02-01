// ============================================
// INFORMACIÓN DE LA BD
// ============================================

const productsData = $('getProducts').first().json.data;
const customersData = $('getCustomers').first().json.data;

// ============================================
// FUNCIONES AUXILIARES
// ============================================

/**
 * Normaliza texto para comparación (elimina acentos, convierte a mayúsculas, etc.)
 */
function normalizeText(text) {
  if (!text) return '';
  return text
    .toString()
    .toUpperCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '') // Elimina acentos
    .replace(/[^A-Z0-9\s]/g, ' ')   // Elimina caracteres especiales
    .replace(/\s+/g, ' ')           // Normaliza espacios
    .trim();
}

/**
 * Calcula similitud entre dos textos usando distancia de Levenshtein
 * (Se mantiene solo para la búsqueda de clientes)
 */
function calculateSimilarity(str1, str2) {
  if (!str1 || !str2) return 0;
  
  const s1 = normalizeText(str1);
  const s2 = normalizeText(str2);
  
  if (s1 === s2) return 1.0;
  if (s1.length === 0 || s2.length === 0) return 0;
  
  const matrix = [];
  for (let i = 0; i <= s2.length; i++) {
    matrix[i] = [i];
  }
  for (let j = 0; j <= s1.length; j++) {
    matrix[0][j] = j;
  }
  for (let i = 1; i <= s2.length; i++) {
    for (let j = 1; j <= s1.length; j++) {
      if (s2.charAt(i - 1) === s1.charAt(j - 1)) {
        matrix[i][j] = matrix[i - 1][j - 1];
      } else {
        matrix[i][j] = Math.min(
          matrix[i - 1][j - 1] + 1,
          matrix[i][j - 1] + 1,
          matrix[i - 1][j] + 1
        );
      }
    }
  }
  
  const distance = matrix[s2.length][s1.length];
  const maxLength = Math.max(s1.length, s2.length);
  return 1 - (distance / maxLength);
}

/**
 * Extrae los nombres alternativos del campo description
 */
function extractDescriptionVariants(description) {
  if (!description) return [];
  
  return description
    .split(';')
    .map(variant => variant.trim())
    .filter(variant => variant.length > 0);
}

// ============================================
// BÚSQUEDA DE PRODUCTOS — SOLO EXACTA
// ============================================

/**
 * Busca producto ÚNICAMENTE por coincidencia exacta de nombre.
 * Compara contra: name y cada variante de description.
 * Si no hay coincidencia exacta, retorna null (no relaciona).
 */
function findProduct(productName) {
  if (!productName) return null;
  
  const searchTerm = normalizeText(productName);
  
  // Estrategia 1: Coincidencia exacta en name
  const exactNameMatch = productsData.find(p => 
    normalizeText(p.name) === searchTerm
  );
  if (exactNameMatch) {
    return { 
      product: exactNameMatch, 
      confidence: 1.0, 
      method: 'exact_name',
      matched_field: 'name'
    };
  }
  
  // Estrategia 2: Coincidencia exacta en alguna variante de description
  for (const product of productsData) {
    const variants = extractDescriptionVariants(product.description);
    
    for (const variant of variants) {
      if (normalizeText(variant) === searchTerm) {
        return { 
          product: product, 
          confidence: 1.0, 
          method: 'exact_description',
          matched_field: 'description',
          matched_variant: variant
        };
      }
    }
  }
  
  // Sin coincidencia exacta → no relaciona
  return null;
}

// ============================================
// BÚSQUEDA DE CLIENTES — SE MANTIENE COMPLETA
// ============================================

/**
 * Búsqueda mejorada de clientes
 * PRIORIDAD: nombre > tax_id exacto > tax_id parcial
 */
function findCustomerEnhanced(customerInfo) {
  const { name, tax_id } = customerInfo;
  
  // Estrategia 1: Búsqueda por NOMBRE (PRIORIDAD MÁS ALTA)
  if (name) {
    const normalizedName = normalizeText(name);
    const exactNameMatch = customersData.find(c => 
      normalizeText(c.name) === normalizedName
    );
    if (exactNameMatch) {
      return { 
        supplier_id: exactNameMatch.id, 
        confidence: 1.0, 
        method: 'name_exact',
        matched_customer: exactNameMatch
      };
    }
    
    // Similitud alta por nombre (>= 0.85)
    let bestMatch = null;
    let bestScore = 0;
    
    customersData.forEach(customer => {
      const score = calculateSimilarity(name, customer.name);
      if (score > bestScore && score >= 0.85) {
        bestScore = score;
        bestMatch = customer;
      }
    });
    
    if (bestMatch) {
      return { 
        supplier_id: bestMatch.id, 
        confidence: bestScore, 
        method: 'name_similarity_high',
        matched_customer: bestMatch
      };
    }
    
    // Palabras clave en nombre
    const nameWords = normalizedName.split(/\s+/).filter(w => w.length > 3);
    let bestContainsMatch = null;
    let bestContainsScore = 0;
    
    customersData.forEach(customer => {
      const customerWords = normalizeText(customer.name).split(/\s+/);
      let matchedWords = 0;
      
      nameWords.forEach(word => {
        if (customerWords.some(cw => cw.includes(word) || word.includes(cw))) {
          matchedWords++;
        }
      });
      
      const score = matchedWords / Math.max(nameWords.length, customerWords.length);
      if (score > bestContainsScore && score >= 0.6) {
        bestContainsScore = score;
        bestContainsMatch = customer;
      }
    });
    
    if (bestContainsMatch) {
      return { 
        supplier_id: bestContainsMatch.id, 
        confidence: 0.8 + (bestContainsScore * 0.15), 
        method: 'name_keywords',
        matched_customer: bestContainsMatch
      };
    }
  }
  
  // Estrategia 2: Tax ID exacto
  if (tax_id) {
    const normalizedTaxId = normalizeText(tax_id);
    const exactTaxMatch = customersData.find(c => 
      normalizeText(c.tax_id) === normalizedTaxId
    );
    if (exactTaxMatch) {
      return { 
        supplier_id: exactTaxMatch.id, 
        confidence: 1.0, 
        method: 'tax_id_exact',
        matched_customer: exactTaxMatch
      };
    }
  }
  
  // Estrategia 3: Similitud media por nombre (0.6 - 0.85)
  if (name) {
    let bestMatch = null;
    let bestScore = 0;
    
    customersData.forEach(customer => {
      const score = calculateSimilarity(name, customer.name);
      if (score > bestScore && score >= 0.6 && score < 0.85) {
        bestScore = score;
        bestMatch = customer;
      }
    });
    
    if (bestMatch) {
      return { 
        supplier_id: bestMatch.id, 
        confidence: bestScore, 
        method: 'name_similarity_medium',
        matched_customer: bestMatch
      };
    }
  }
  
  // Estrategia 4: Tax ID parcial (solo si no hay nombre)
  if (tax_id && !name) {
    const normalizedTaxId = normalizeText(tax_id);
    const partialTaxMatch = customersData.find(c => 
      normalizeText(c.tax_id).includes(normalizedTaxId) ||
      normalizedTaxId.includes(normalizeText(c.tax_id))
    );
    if (partialTaxMatch) {
      return { 
        supplier_id: partialTaxMatch.id, 
        confidence: 0.7, 
        method: 'tax_id_partial',
        matched_customer: partialTaxMatch
      };
    }
  }
  
  return null;
}

// ============================================
// PROCESAMIENTO PRINCIPAL
// ============================================

function processWithEnhancedSearch(inputData) {
  const results = [];
  
  for (const item of inputData) {
    let aiResponse;
    
    if (item.json.message && item.json.message.content) {
      const content = item.json.message.content;
      const jsonMatch = content.match(/```json\n([\s\S]*?)\n```/);
      aiResponse = jsonMatch ? JSON.parse(jsonMatch[1]) : JSON.parse(content);
    } else if (item.json[0] && item.json[0].message) {
      const content = item.json[0].message.content;
      const jsonMatch = content.match(/```json\n([\s\S]*?)\n```/);
      aiResponse = JSON.parse(jsonMatch[1]);
    } else {
      aiResponse = item.json;
    }
    
    console.log('Respuesta de OpenAI:', JSON.stringify(aiResponse, null, 2));
    
    // Buscar cliente
    const customerMatch = findCustomerEnhanced(aiResponse.customer_info);
    
    // Procesar items — solo relaciona si hay coincidencia exacta
    const transformedItems = [];
    const unmatchedProducts = [];
    
    for (const aiItem of aiResponse.items) {
      const productMatch = findProduct(aiItem.product_name);
      
      if (productMatch) {
        transformedItems.push({
          product_id: productMatch.product.id,
          quantity: aiItem.quantity,
          unit_price: aiItem.unit_price || productMatch.product.price,
          notes: aiItem.notes || '',
          _metadata: {
            original_name: aiItem.product_name,
            matched_name: productMatch.product.name,
            matched_field: productMatch.matched_field,
            matched_variant: productMatch.matched_variant || null,
            confidence: productMatch.confidence,
            method: productMatch.method
          }
        });
      } else {
        // No hay coincidencia exacta → va a no relacionados
        unmatchedProducts.push(aiItem.product_name);
      }
    }
    
    // Objeto final
    const transformed = {
      id: null,
      supplier_id: customerMatch ? customerMatch.supplier_id : null,
      expected_delivery_date: aiResponse.order_info.expected_delivery_date,
      notes: aiResponse.order_info.notes || '',
      tax: 0,
      shipping: 0,
      items: transformedItems
    };
    
    const result = {
      ...transformed,
      _metadata: {
        customer: {
          original: aiResponse.customer_info,
          matched: customerMatch ? customerMatch.matched_customer : null,
          confidence: customerMatch ? customerMatch.confidence : 0,
          method: customerMatch ? customerMatch.method : 'not_found'
        },
        supplier: aiResponse.supplier_info || null,
        products: {
          total: aiResponse.items.length,
          matched: transformedItems.length,
          unmatched: unmatchedProducts.length
        },
        unmatched_products: unmatchedProducts,
        order_info: {
          order_number: aiResponse.order_info.order_number,
          order_date: aiResponse.order_info.order_date
        },
        ready_to_process: customerMatch && transformedItems.length > 0,
        warnings: []
      }
    };
    
    // Advertencias
    if (!customerMatch) {
      result._metadata.warnings.push('Cliente no encontrado');
    } else if (customerMatch.confidence < 0.8) {
      result._metadata.warnings.push(`Cliente con baja confianza: ${customerMatch.confidence.toFixed(2)}`);
    }
    
    if (unmatchedProducts.length > 0) {
      result._metadata.warnings.push(`${unmatchedProducts.length} productos sin coincidencia exacta: ${unmatchedProducts.join(', ')}`);
    }
    
    results.push({ json: result });
  }
  
  return results;
}

// ============================================
// EJECUCIÓN
// ============================================

return processWithEnhancedSearch($input.all());