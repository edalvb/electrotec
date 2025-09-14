'use client'
import { Flex } from '@radix-ui/themes'
import SummaryCard from './SummaryCard'
import TechnicianCard from './TechnicianCard'
import ConfirmCard from './ConfirmCard'
import ErrorMessage from './ErrorMessage'

export default function ReviewStep() {
  return (
    <Flex direction="column" gap="4">
      <SummaryCard/>
      <TechnicianCard/>
      <ConfirmCard/>
      <ErrorMessage/>
    </Flex>
  )
}
