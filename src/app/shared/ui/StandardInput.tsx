import { ChangeEvent, ReactNode, useState } from 'react'

type Props = {
  value: string
  onChange: (e: ChangeEvent<HTMLInputElement>) => void
  placeholder?: string
  type?: 'text' | 'email' | 'password' | 'number' | 'date' | 'tel' | 'url'
  label?: string
  error?: string
  disabled?: boolean
  className?: string
  min?: number | string
  max?: number | string
  step?: number | string
  name?: string
  icon?: ReactNode
  helperText?: string
  required?: boolean
  autoComplete?: string
  maxLength?: number
}

export default function StandardInput({ 
  value, 
  onChange, 
  placeholder, 
  type = 'text', 
  label, 
  error, 
  disabled = false, 
  className = '', 
  min, 
  max, 
  step, 
  name,
  icon,
  helperText,
  required = false,
  autoComplete,
  maxLength
}: Props) {
  const [isFocused, setIsFocused] = useState(false)

  return (
    <div className={`space-y-2 ${className}`}>
      {label && (
        <label className="block text-sm font-medium text-white/90">
          {label}
          {required && <span className="text-red-400 ml-1">*</span>}
        </label>
      )}
      
      <div className="relative group">
        {icon && (
          <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
            <div className={`transition-colors duration-200 ${
              isFocused 
                ? 'text-white/80' 
                : error 
                  ? 'text-red-400' 
                  : 'text-white/50'
            }`}>
              {icon}
            </div>
          </div>
        )}
        
        <input
          className={`
            input-glass w-full transition-all duration-200 ease-in-out
            ${icon ? 'pl-10 pr-4' : 'px-4'} py-3
            ${isFocused ? 'scale-[1.01] shadow-lg' : ''}
            ${error 
              ? 'border-red-500/70 focus:border-red-500 focus:shadow-red-500/25' 
              : 'focus:border-blue-400/70 focus:shadow-blue-500/25'
            }
            ${disabled 
              ? 'opacity-60 cursor-not-allowed bg-black/10' 
              : 'hover:border-white/50'
            }
            placeholder:text-white/40 placeholder:transition-colors
            focus:placeholder:text-white/60
          `}
          value={value}
          onChange={onChange}
          onFocus={() => setIsFocused(true)}
          onBlur={() => setIsFocused(false)}
          placeholder={placeholder}
          type={type}
          disabled={disabled}
          min={min as any}
          max={max as any}
          step={step as any}
          name={name}
          required={required}
          autoComplete={autoComplete}
          maxLength={maxLength}
        />
        
        {/* Focus ring effect */}
        <div className={`
          absolute inset-0 rounded-xl pointer-events-none transition-opacity duration-200
          ${isFocused && !error ? 'opacity-100' : 'opacity-0'}
          bg-gradient-to-r from-blue-500/10 to-purple-500/10
        `} />
      </div>
      
      {/* Helper text or error */}
      {(error || helperText) && (
        <div className="space-y-1">
          {error && (
            <div className="flex items-center gap-2 text-red-400 text-sm animate-in slide-in-from-left-2 duration-200">
              <svg className="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>{error}</span>
            </div>
          )}
          {helperText && !error && (
            <div className="text-white/60 text-sm">
              {helperText}
            </div>
          )}
        </div>
      )}
      
      {/* Character count for inputs with maxLength */}
      {maxLength && type === 'text' && (
        <div className="flex justify-end">
          <span className={`text-xs transition-colors ${
            value.length > maxLength * 0.8 
              ? value.length >= maxLength 
                ? 'text-red-400' 
                : 'text-yellow-400'
              : 'text-white/40'
          }`}>
            {value.length}/{maxLength}
          </span>
        </div>
      )}
    </div>
  )
}
