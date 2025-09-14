import { Dialog } from '@radix-ui/themes'
import { ReactNode } from 'react'
import ModernButton from './ModernButton'

interface ModernModalProps {
  open: boolean
  onOpenChange: (open: boolean) => void
  title: string
  description?: string
  children: ReactNode
  size?: 'sm' | 'md' | 'lg' | 'xl'
  showCloseButton?: boolean
  className?: string
}

export default function ModernModal({
  open,
  onOpenChange,
  title,
  description,
  children,
  size = 'md',
  showCloseButton = true,
  className = ''
}: ModernModalProps) {
  const sizeClasses = {
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl'
  }

  return (
    <Dialog.Root open={open} onOpenChange={onOpenChange}>
      <Dialog.Content 
        className={`
          modal-glass border border-white/20 backdrop-blur-lg
          ${sizeClasses[size]} w-full mx-4
          ${className}
        `}
      >
        <div className="flex justify-between items-start mb-6">
          <div className="space-y-2">
            <Dialog.Title className="text-xl font-semibold text-white">
              {title}
            </Dialog.Title>
            {description && (
              <Dialog.Description className="text-white/70">
                {description}
              </Dialog.Description>
            )}
          </div>
          {showCloseButton && (
            <Dialog.Close>
              <ModernButton
                variant="glass"
                size="sm"
                className="p-2 hover:bg-white/10"
              >
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                </svg>
              </ModernButton>
            </Dialog.Close>
          )}
        </div>
        
        <div className="space-y-4">
          {children}
        </div>
      </Dialog.Content>
    </Dialog.Root>
  )
}