'use client'
import { useState } from 'react'
import { Flex } from '@radix-ui/themes'
import EquipmentSearch from './EquipmentSearch'
import SelectedEquipmentCard from './SelectedEquipmentCard'
import NewEquipmentModal from './NewEquipmentModal'

export default function EquipmentStep() {
  const [showNew, setShowNew] = useState(false)
  return (
    <Flex direction="column" gap="4">
      <EquipmentSearch onOpenNew={() => setShowNew(true)} />
      <SelectedEquipmentCard />
      {showNew && <NewEquipmentModal onClose={() => setShowNew(false)} />}
    </Flex>
  )
}
