import { Button as RadixButton } from '@radix-ui/themes'
import { ReactNode } from 'react'

interface ModernButtonProps {
  children: ReactNode
  variant?: 'primary' | 'secondary' | 'glass' | 'danger'
  size?: 'sm' | 'md' | 'lg'
  icon?: ReactNode
  loading?: boolean
  disabled?: boolean
  onClick?: () => void
  className?: string
}

export default function ModernButton({
  children,
  variant = 'primary',
  size = 'md',
  icon,
  loading = false,
  disabled = false,
  onClick,
  className = ''
}: ModernButtonProps) {
  const baseClasses = 'inline-flex items-center justify-center gap-2 font-medium transition-all duration-200 transform active:scale-95 disabled:opacity-60 disabled:cursor-not-allowed'
  
  const variantClasses = {
    primary: 'bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white border-0 hover:scale-105 shadow-lg hover:shadow-xl',
    secondary: 'bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-700 hover:to-teal-700 text-white border-0 hover:scale-105 shadow-lg hover:shadow-xl',
    glass: 'backdrop-filter backdrop-blur-8 bg-white/25 border border-white/40 text-white hover:bg-white/35 hover:scale-105',
    danger: 'bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white border-0 hover:scale-105 shadow-lg hover:shadow-xl'
  }
  
  const sizeClasses = {
    sm: 'px-3 py-1.5 text-sm rounded-lg',
    md: 'px-4 py-2.5 text-base rounded-xl',
    lg: 'px-6 py-3 text-lg rounded-xl'
  }

  return (
    <RadixButton
      className={`${baseClasses} ${variantClasses[variant]} ${sizeClasses[size]} ${className}`}
      disabled={disabled || loading}
      onClick={onClick}
    >
      {loading ? (
        <>
          <div className="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin" />
          {children}
        </>
      ) : (
        <>
          {icon && icon}
          {children}
        </>
      )}
    </RadixButton>
  )
}