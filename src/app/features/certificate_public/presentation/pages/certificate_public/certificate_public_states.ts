import { create } from 'zustand'

type Equipment = { brand: string; model: string; serial_number: string }
type EquipmentType = { name: string }
type Client = { name: string }
type Technician = { full_name: string; signature_image_url: string | null }

type Cert = { id: string; certificate_number: string; calibration_date: string; next_calibration_date: string; pdf_url: string | null; equipment: Equipment | null; equipment_type: EquipmentType | null; client: Client | null; technician: Technician | null }

type State = { data: Cert | null; setData: (c: Cert | null) => void }

export const useCertificatePublicState = create<State>((set) => ({ data: null, setData: c => set({ data: c }) }))
