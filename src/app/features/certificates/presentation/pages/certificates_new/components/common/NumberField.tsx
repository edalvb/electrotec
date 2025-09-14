'use client'
import { TextField } from '@radix-ui/themes'

export default function NumberField({ value, onChange, placeholder }: { value: number | undefined; onChange: (n: number | undefined) => void; placeholder: string }) {
  return <TextField.Root value={value == null ? '' : String(value)} onChange={e => { const v = e.target.value; onChange(v === '' ? undefined : Number(v)) }} placeholder={placeholder}/>
}
