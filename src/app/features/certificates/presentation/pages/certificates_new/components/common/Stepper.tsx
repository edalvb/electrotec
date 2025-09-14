'use client'

export default function Stepper({ step }: { step: 1 | 2 | 3 }) {
  const items = [
    { n: 1, t: 'Seleccionar Equipo', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z' },
    { n: 2, t: 'Datos de Calibración', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01' },
    { n: 3, t: 'Revisar y Generar', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }
  ]
  
  return (
    <div className="relative">
      {/* Progress Line */}
      <div className="absolute top-6 left-0 right-0 h-0.5 bg-white/20 z-0">
        <div 
          className="h-full bg-gradient-to-r from-blue-400 to-purple-400 transition-all duration-500"
          style={{ width: `${((step - 1) / 2) * 100}%` }}
        />
      </div>
      
      {/* Steps */}
      <div className="grid grid-cols-3 gap-4 relative z-10">
        {items.map(item => {
          const isCompleted = step > item.n
          const isCurrent = step === item.n
          // const isPending = step < item.n // not used currently, kept for potential future logic
          
          return (
            <div key={item.n} className="flex flex-col items-center space-y-3">
              {/* Step Circle */}
              <div className={`
                w-12 h-12 rounded-full flex items-center justify-center transition-all duration-300 border-2
                ${isCurrent 
                  ? 'bg-gradient-to-br from-blue-500 to-purple-600 border-blue-400 shadow-lg shadow-blue-500/25' 
                  : isCompleted 
                    ? 'bg-gradient-to-br from-green-500 to-emerald-600 border-green-400' 
                    : 'bg-white/10 border-white/30 backdrop-blur-sm'
                }
              `}>
                {isCompleted ? (
                  <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                  </svg>
                ) : (
                  <svg 
                    className={`w-6 h-6 ${isCurrent ? 'text-white' : 'text-white/60'}`} 
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                  >
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d={item.icon} />
                  </svg>
                )}
              </div>
              
              {/* Step Label */}
              <div className="text-center">
                <div className={`
                  text-sm font-medium transition-colors duration-300
                  ${isCurrent 
                    ? 'text-white' 
                    : isCompleted 
                      ? 'text-green-300' 
                      : 'text-white/60'
                  }
                `}>
                  {item.t}
                </div>
                <div className={`
                  text-xs transition-colors duration-300
                  ${isCurrent 
                    ? 'text-blue-300' 
                    : isCompleted 
                      ? 'text-green-400' 
                      : 'text-white/40'
                  }
                `}>
                  Paso {item.n}
                </div>
              </div>
            </div>
          )
        })}
      </div>
    </div>
  )
}
