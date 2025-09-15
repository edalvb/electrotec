import { create } from 'zustand'

type Client = { id: string; name: string }
type Equipment = { id: string; serial_number: string; brand: string; model: string; equipment_type: { id: number; name: string } | null }

type Lab = { temperature?: number; humidity?: number; pressure?: number; calibration?: boolean; maintenance?: boolean }

type AngularRow = { pattern: string; obtained: string; error: string }
type DistanceRow = { control: number; obtained: number; delta: number }
type LevelRow = { pattern: { d: number; m: number; s: number }; obtained: { d: number; m: number; s: number }; precision: number; precision_unit: 'mm' | '"'; error: string }

type Results = {
  angular_precision?: string
  angular_measurements?: AngularRow[]
  prism_measurements?: DistanceRow[]
  no_prism_measurements?: DistanceRow[]
  distance_precision?: string
  level_precision_mm?: number
  level_error?: string
  level_rows?: LevelRow[]
}

type Errors = Record<string, string>

type State = {
  isOpen: boolean
  isLoading: boolean
  client: Client | null
  clients: Client[]
  clientsLoading: boolean
  clientsPage: number
  clientsPageSize: number
  clientsTotal: number
  clientsTotalPages: number
  clientQuery: string
  confirmClientId: string | null
  equipmentList: Equipment[]
  equipmentId: string
  calibrationDate: string
  nextCalibrationDate: string
  lab: Lab
  results: Results
  errors: Errors
  technicianId: string | null
  set: (s: Partial<State>) => void
  reset: () => void
}

const initial: Omit<State, 'set' | 'reset'> = {
  isOpen: false,
  isLoading: false,
  client: null,
  clients: [],
  clientsLoading: false,
  clientsPage: 1,
  clientsPageSize: 50,
  clientsTotal: 0,
  clientsTotalPages: 1,
  clientQuery: '',
  confirmClientId: null,
  equipmentList: [],
  equipmentId: '',
  calibrationDate: '',
  nextCalibrationDate: '',
  lab: {},
  results: {},
  errors: {},
  technicianId: null
}

export const useCertificatesModalState = create<State>((set) => ({
  ...initial,
  set: (s) => set(s),
  reset: () => set(initial)
}))
