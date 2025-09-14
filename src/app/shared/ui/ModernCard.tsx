import { Card } from '@radix-ui/themes'
import { ReactNode } from 'react'

interface ModernCardProps {
  children: ReactNode
  variant?: 'glass' | 'solid' | 'gradient'
  padding?: 'sm' | 'md' | 'lg'
  hover?: boolean
  className?: string
}

export default function ModernCard({
  children,
  variant = 'glass',
  padding = 'md',
  hover = false,
  className = ''
}: ModernCardProps) {
  const baseClasses = 'transition-all duration-300'
  
  const variantClasses = {
    glass: 'backdrop-filter backdrop-blur-xl bg-white/10 border-2 border-white/10 shadow-2xl',
    solid: 'bg-slate-800/90 border-2 border-slate-700/50',
    gradient: 'bg-gradient-to-br from-blue-900/30 via-purple-900/30 to-pink-900/30 border-2 border-white/20'
  }
  
  const paddingClasses = {
    sm: 'p-4',
    md: 'p-6',
    lg: 'p-8'
  }
  
  const hoverClasses = hover ? 'hover:scale-[1.02] hover:border-white/30 hover:shadow-3xl cursor-pointer' : ''

  return (
    <Card className={`${baseClasses} ${variantClasses[variant]} ${paddingClasses[padding]} ${hoverClasses} ${className}`}>
      {children}
    </Card>
  )
}