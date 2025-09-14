'use client'
import { useEffect } from 'react'
import { Card, Flex, Text } from '@radix-ui/themes'
import { useCertificatesNew } from '../../Certificates_new_context'
import { useCertificatesNewState } from '../../certificates_new_states'
import DatesCard from './DatesCard'
import LabConditionsCard from './LabConditionsCard'
import AngularCalibration from './AngularCalibration'
import DistancePrecision from './DistancePrecision'
import LevelSection from './LevelSection'

export default function CalibrationStep() {
  const { controller } = useCertificatesNew()
  const s = useCertificatesNewState(st => st)
  const typeName = s.selectedEquipment?.equipment_type?.name || ''
  useEffect(() => { if (typeName === 'Nivel') { controller.setResults({ level_precision_mm: s.results.level_precision_mm || undefined }) } }, [controller, s.results.level_precision_mm, typeName])
  return (
    <Flex direction="column" gap="4">
      <DatesCard/>
      <LabConditionsCard/>
      <Card className="glass p-6 border-2 border-white/20 bg-white/5 backdrop-blur-xl">
        <div className="flex items-center gap-3 mb-6">
          <div className="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500/20 to-pink-500/20 flex items-center justify-center">
            <svg className="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
          </div>
          <Text size="4" className="font-semibold text-white">Calibración y Ajustes</Text>
        </div>
        <Flex direction="column" gap="6">
          {['Teodolito', 'Estación Total'].includes(typeName) && <AngularCalibration/>}
          {typeName === 'Estación Total' && <DistancePrecision/>}
          {typeName === 'Nivel' && <LevelSection/>}
        </Flex>
      </Card>
    </Flex>
  )
}
