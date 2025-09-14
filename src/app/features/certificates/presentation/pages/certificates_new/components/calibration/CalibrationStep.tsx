'use client'
import { useEffect } from 'react'
import { Card, Flex } from '@radix-ui/themes'
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
      <Card className="glass p-4">
        <Flex direction="column" gap="3">
          {['Teodolito', 'Estación Total'].includes(typeName) && <AngularCalibration/>}
          {typeName === 'Estación Total' && <DistancePrecision/>}
          {typeName === 'Nivel' && <LevelSection/>}
        </Flex>
      </Card>
    </Flex>
  )
}
