import { create } from 'zustand'

type Lab = { temperature?: number; humidity?: number; pressure?: number }
type EquipmentSel = { id: string; serial_number: string; brand: string; model: string; client: { id: string; name: string } | null; equipment_type: { id: number; name: string } | null } | null

type AngularRow = { pattern: string; obtained: string; error: string }
type DistanceRow = { control: number; obtained: number; delta: number }

type Results = {
  angular_precision?: string
  angular_measurements?: AngularRow[]
  prism_measurements?: DistanceRow[]
  no_prism_measurements?: DistanceRow[]
  distance_precision?: string
  level_precision_mm?: number
  level_error?: string
}

type Errors = Record<string, string>

type State = {
  step: 1 | 2 | 3
  isLoading: boolean
  equipmentQuery: string
  equipmentSuggestions: EquipmentSel[]
  selectedEquipment: EquipmentSel
  calibrationDate: string
  nextCalibrationDate: string
  lab: Lab
  results: Results
  confirmed: boolean
  errors: Errors
  technician: { full_name: string; signature_image_url: string | null } | null
  set: (s: Partial<State>) => void
  reset: () => void
}

const initial: Omit<State, 'set' | 'reset'> = {
  step: 1,
  isLoading: false,
  equipmentQuery: '',
  equipmentSuggestions: [],
  selectedEquipment: null,
  calibrationDate: '',
  nextCalibrationDate: '',
  lab: {},
  results: {},
  confirmed: false,
  errors: {},
  technician: null
}

export const useCertificatesNewState = create<State>((set) => ({
  ...initial,
  set: (s) => set(s),
  reset: () => set(initial)
}))
