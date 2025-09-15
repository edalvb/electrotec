import React from 'react'
import { Table } from '@radix-ui/themes'

// Función helper para concatenar clases
const cn = (...classes: (string | undefined | null | false)[]): string => {
  return classes.filter(Boolean).join(' ')
}

interface ModernTableProps {
  children: React.ReactNode
  className?: string
}

interface ModernTableHeaderProps {
  children: React.ReactNode
  className?: string
}

interface ModernTableBodyProps {
  children: React.ReactNode
  className?: string
}

interface ModernTableRowProps {
  children: React.ReactNode
  className?: string
  hover?: boolean
}

interface ModernTableCellProps {
  children: React.ReactNode
  className?: string
  header?: boolean
  colSpan?: number
}

const ModernTable = React.forwardRef<HTMLTableElement, ModernTableProps>(
  ({ children, className, ...props }, ref) => {
    return (
      <div className="overflow-x-auto">
        <Table.Root 
          ref={ref}
          className={cn(
            "w-full border-separate border-spacing-0",
            className
          )} 
          {...props}
        >
          {children}
        </Table.Root>
      </div>
    )
  }
)
ModernTable.displayName = 'ModernTable'

const ModernTableHeader = React.forwardRef<HTMLTableSectionElement, ModernTableHeaderProps>(
  ({ children, className, ...props }, ref) => {
    return (
      <Table.Header 
        ref={ref}
        className={cn(
          "bg-gradient-to-r from-slate-800/80 to-slate-700/80 backdrop-blur-sm",
          className
        )}
        {...props}
      >
        {children}
      </Table.Header>
    )
  }
)
ModernTableHeader.displayName = 'ModernTableHeader'

const ModernTableBody = React.forwardRef<HTMLTableSectionElement, ModernTableBodyProps>(
  ({ children, className, ...props }, ref) => {
    return (
      <Table.Body 
        ref={ref}
        className={cn("bg-slate-900/40 backdrop-blur-sm", className)}
        {...props}
      >
        {children}
      </Table.Body>
    )
  }
)
ModernTableBody.displayName = 'ModernTableBody'

const ModernTableRow = React.forwardRef<HTMLTableRowElement, ModernTableRowProps>(
  ({ children, className, hover = true, ...props }, ref) => {
    return (
      <Table.Row
        ref={ref}
        className={cn(
          "border-b border-slate-700/50 transition-all duration-200",
          hover && "hover:bg-slate-800/60 hover:backdrop-blur-md",
          "first:border-t-0",
          className
        )}
        {...props}
      >
        {children}
      </Table.Row>
    )
  }
)
ModernTableRow.displayName = 'ModernTableRow'

const ModernTableCell = React.forwardRef<HTMLTableCellElement, ModernTableCellProps>(
  ({ children, className, header = false, colSpan, ...props }, ref) => {
    const CellComponent = header ? Table.ColumnHeaderCell : Table.Cell
    
    return (
      <CellComponent
        ref={ref}
        colSpan={colSpan}
        className={cn(
          "px-6 py-4 text-sm font-medium transition-colors",
          header 
            ? "text-slate-200 font-semibold tracking-wide uppercase text-xs bg-gradient-to-r from-slate-800/90 to-slate-700/90 border-b-2 border-blue-500/30" 
            : "text-slate-100 hover:text-white",
          "first:border-l-0 last:border-r-0",
          "border-slate-700/30",
          className
        )}
        {...props}
      >
        {children}
      </CellComponent>
    )
  }
)
ModernTableCell.displayName = 'ModernTableCell'

export {
  ModernTable,
  ModernTableHeader,
  ModernTableBody,
  ModernTableRow,
  ModernTableCell
}