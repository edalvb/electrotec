'use client'
import { createClient } from '@supabase/supabase-js'

export function supabaseBrowser() {
	const url = process.env.NEXT_PUBLIC_SUPABASE_URL as string
	const anon = process.env.NEXT_PUBLIC_SUPABASE_ANON_KEY as string
	return createClient(url, anon)
}
