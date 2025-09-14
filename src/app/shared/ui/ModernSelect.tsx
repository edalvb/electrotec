import { Select } from '@radix-ui/themes'
import { ReactNode } from 'react'

interface ModernSelectProps {
  value: string
  onValueChange: (value: string) => void
  placeholder?: string
  label?: string
  icon?: ReactNode
  error?: string
  disabled?: boolean
  className?: string
  children: ReactNode
}

export default function ModernSelect({
  value,
  onValueChange,
  placeholder,
  label,
  icon,
  error,
  disabled = false,
  className = '',
  children
}: ModernSelectProps) {
  return (
    <div className={`space-y-2 ${className}`}>
      {label && (
        <label className="block text-sm font-medium text-white/90">
          {label}
        </label>
      )}
      <div className="relative">
        {icon && (
          <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
            <div className="text-white/50">
              {icon}
            </div>
          </div>
        )}
        <Select.Root 
          value={value}
          onValueChange={onValueChange}
          disabled={disabled}
        >
          <Select.Trigger
            className={`
              input-glass transition-all duration-200 focus:scale-[1.02] w-full
              ${icon ? 'pl-10' : 'pl-4'}
              ${error ? 'border-red-500/70 focus:border-red-500' : ''}
            `}
            placeholder={placeholder}
          />
          <Select.Content className="select-glass border border-white/20 backdrop-blur-md bg-slate-900/70">
            {children}
          </Select.Content>
        </Select.Root>
      </div>
      {error && (
        <div className="flex items-center gap-2 text-red-400 text-sm">
          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          {error}
        </div>
      )}
    </div>
  )
}

ModernSelect.Item = Select.Item
ModernSelect.Separator = Select.Separator