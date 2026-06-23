<?php

/**
 * ============================================================================
 *                    VPC & NETWORKING — MUKAMMAL GUIDE
 *          VPC, Subnets, Security Groups, aur Network Architecture
 * ============================================================================
 */


// =============================================================================
// 1. VPC KYA HAI?
// =============================================================================

/*
 * VPC = Virtual Private Cloud
 *
 * VPC AWS mein aap ka apna PRIVATE network hai.
 *
 * AASAN MISAAL:
 * ─────────────
 *   VPC = Aap ki company ki building
 *   Subnets = Building ke floors
 *   Security Groups = Har kamre ke darban
 *   Internet Gateway = Building ka main darwaza
 *   NAT Gateway = Peeche ka darwaza (bahar ja sakte ho magar bahar se andar nahi aa sakte)
 *
 *
 * PRODUCTION VPC ARCHITECTURE:
 *
 *   ┌──────────────────────────────────────────────────────────────┐
 *   │  VPC (10.0.0.0/16)                                          │
 *   │                                                              │
 *   │  ┌─────────────────────────────────────────────────────┐    │
 *   │  │  PUBLIC SUBNETS (Internet se accessible)             │    │
 *   │  │                                                      │    │
 *   │  │  Subnet 1a (10.0.1.0/24)  │  Subnet 1b (10.0.2.0/24)│   │
 *   │  │  [ALB]  [NAT Gateway]     │  [ALB]                   │   │
 *   │  └─────────────────────────────────────────────────────┘    │
 *   │                                                              │
 *   │  ┌─────────────────────────────────────────────────────┐    │
 *   │  │  PRIVATE SUBNETS (Internet se access NAHI)           │    │
 *   │  │                                                      │    │
 *   │  │  Subnet 2a (10.0.3.0/24)  │  Subnet 2b (10.0.4.0/24)│   │
 *   │  │  [EC2 — Laravel]          │  [EC2 — Laravel]         │   │
 *   │  └─────────────────────────────────────────────────────┘    │
 *   │                                                              │
 *   │  ┌─────────────────────────────────────────────────────┐    │
 *   │  │  DATABASE SUBNETS (Sab se private)                   │    │
 *   │  │                                                      │    │
 *   │  │  Subnet 3a (10.0.5.0/24)  │  Subnet 3b (10.0.6.0/24)│   │
 *   │  │  [RDS — Primary]          │  [RDS — Standby]         │   │
 *   │  │  [ElastiCache]            │  [ElastiCache Replica]   │   │
 *   │  └─────────────────────────────────────────────────────┘    │
 *   │                                                              │
 *   │  [Internet Gateway] ← Bahar ki duniya se connection         │
 *   └──────────────────────────────────────────────────────────────┘
 *
 *
 * SECURITY GROUPS (Firewall Rules):
 *
 *   ALB Security Group:
 *     Inbound: HTTP(80), HTTPS(443) ← 0.0.0.0/0 (sab se)
 *
 *   App Security Group (EC2):
 *     Inbound: HTTP(80) ← Sirf ALB Security Group se
 *     Inbound: SSH(22)  ← Sirf apne IP se
 *
 *   DB Security Group (RDS):
 *     Inbound: MySQL(3306) ← Sirf App Security Group se
 *
 *   Redis Security Group:
 *     Inbound: Redis(6379) ← Sirf App Security Group se
 *
 *
 * ⚠️ AHEM USOOL:
 *   - ALB public subnet mein
 *   - App servers private subnet mein
 *   - Database sab se private subnet mein
 *   - Security groups mein sirf zaruri ports kholein
 *   - RDS aur ElastiCache ko KABHI public mat karo
 */
