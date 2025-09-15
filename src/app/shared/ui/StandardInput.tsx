import { ChangeEvent } from 'react'

type Props = {
  value: string
  onChange: (e: ChangeEvent<HTMLInputElement>) => void
  placeholder?: string
  type?: 'text' | 'email' | 'password' | 'number' | 'date'
  label?: string
  error?: string
  disabled?: boolean
  className?: string
  min?: number | string
  max?: number | string
  step?: number | string
  name?: string
}

export default function StandardInput({ value, onChange, placeholder, type = 'text', label, error, disabled = false, className = '', min, max, step, name }: Props) {
  return (
    <div className={`space-y-2 ${className}`}>
      {label && (
        <label className="block text-sm font-medium text-white/90">{label}</label>
      )}
      <input
        className={`input-glass w-full ${error ? 'border-red-500/70 focus:border-red-500' : ''}`}
        value={value}
        onChange={onChange}
        placeholder={placeholder}
        type={type}
        disabled={disabled}
        min={min as any}
        max={max as any}
        step={step as any}
        name={name}
      />
      {error && (
        <div className="flex items-center gap-2 text-red-400 text-sm">{error}</div>
      )}
    </div>
  )
}
